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

        $notifications = NotificationRecipient::where('user_id', $userId)
            ->whereHas('notification') // ✅ Pastikan notifikasi masih ada
            ->with('notification')
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
     * Menandai semua notifikasi sebagai telah dibaca.
     */
    public function markAllAsRead()
    {
        $userId = Auth::id();

        // Tandai semua notifikasi user sebagai telah dibaca
        NotificationRecipient::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
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
