<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationRecipient;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Menampilkan daftar notifikasi user yang sedang login.
     */
    public function index()
    {
        $userId = auth()->id();

        $notifications = Notification::whereHas('recipients', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->orderByRaw('read_at IS NULL DESC, created_at DESC')
            ->paginate(30);

        return view('pages/notifications/index', compact('notifications'));
    }
    /**
     * Menampilkan detail notifikasi dan menandainya sebagai telah dibaca.
     */
    public function show($notification_id)
    {
        $userId = Auth::id();

        $notificationRecipient = NotificationRecipient::where('user_id', $userId)
            ->where('notification_id', $notification_id)
            ->with('notification')
            ->first();

        if (!$notificationRecipient || !$notificationRecipient->notification) {
            return back()->with('error', 'Notifikasi tidak ditemukan atau telah dihapus.');
        }

        if (!$notificationRecipient->read_at) {
            $notificationRecipient->update(['read_at' => now()]);
        }

        return view('pages/notifications/show', ['notification' => $notificationRecipient->notification]);
    }

    /**
     * Menandai notifikasi by id sebagai telah dibaca.
     */
    public function markAsRead($notification_id)
    {
        try {
            $userId = Auth::id();

            // Ambil notifikasi dari tabel notification_recipients
            $notificationRecipient = NotificationRecipient::where('user_id', $userId)
                ->where('notification_id', $notification_id)
                ->first();

            if (!$notificationRecipient) {
                return response()->json(['success' => false, 'message' => 'Notifikasi tidak ditemukan.'], 404);
            }

            // Tandai notifikasi sebagai telah dibaca di tabel notification_recipients
            if (!$notificationRecipient->read_at) {
                $notificationRecipient->update(['read_at' => now()]);
            }

            // Cek apakah semua penerima telah membaca notifikasi ini
            $unreadRecipients = NotificationRecipient::where('notification_id', $notification_id)
                ->whereNull('read_at')
                ->count();

            // Jika tidak ada penerima lain yang belum membaca, tandai notifikasi di tabel notifications
            if ($unreadRecipients === 0) {
                Notification::where('id', $notification_id)
                    ->update(['read_at' => now()]);
            }

            return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sebagai dibaca.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server.'], 500);
        }
    }

    /**
     * Menandai semua notifikasi sebagai telah dibaca.
     */
    public function markAllAsRead()
    {
        try {
            $userId = Auth::id();

            // Tandai semua notifikasi user sebagai telah dibaca di tabel notification_recipients
            NotificationRecipient::where('user_id', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            // Cek semua notifikasi yang sudah tidak memiliki penerima lain yang belum membaca
            $unreadNotifications = Notification::whereHas('recipients', function ($query) {
                $query->whereNull('read_at');
            })->pluck('id')->toArray();

            // Tandai semua notifikasi yang sudah sepenuhnya terbaca di tabel notifications
            Notification::whereNotIn('id', $unreadNotifications)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menandai semua notifikasi sebagai dibaca.');
        }
    }

    /**
     * Menghapus satu notifikasi.
     */
    public function destroy($notification_id)
    {
        $userId = Auth::id();

        // Hapus dari tabel pivot `notification_recipients`
        NotificationRecipient::where('user_id', $userId)
            ->where('notification_id', $notification_id)
            ->delete();

        // Jika tidak ada penerima lain, hapus dari `notifications`
        $remainingRecipients = NotificationRecipient::where('notification_id', $notification_id)->count();

        if ($remainingRecipients === 0) {
            Notification::where('id', $notification_id)->delete();
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Menghapus semua notifikasi user.
     */
    public function clearAll()
    {
        $userId = Auth::id();

        // Hapus semua notifikasi user dari `notification_recipients`
        NotificationRecipient::where('user_id', $userId)->delete();

        // Hapus notifikasi yang sudah tidak memiliki penerima
        Notification::whereDoesntHave('recipients')->delete();

        return back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }

    public function getUnreadNotificationsCount()
    {
        $userId = auth()->id();

        $count = NotificationRecipient::where('user_id', $userId)
            ->whereNull('read_at') // Hanya notifikasi yang belum dibaca
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}
