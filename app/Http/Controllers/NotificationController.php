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
        $userId = Auth::id();

        // Ambil semua notifikasi berdasarkan tabel pivot `notification_recipients`
        $notifications = NotificationRecipient::where('user_id', $userId)
            ->with('notification') // Pastikan relasi dengan tabel notifications
            ->orderBy('created_at', 'asc')
            ->paginate(30); // Pagination untuk setiap 30 notifikasi

        return view('pages/notifications/index', compact('notifications'));
    }

    /**
     * Menampilkan detail notifikasi dan menandainya sebagai telah dibaca.
     */
    public function show($notification_id)
    {
        $userId = Auth::id();

        // Cari notifikasi berdasarkan tabel pivot `notification_recipients`
        $notificationRecipient = NotificationRecipient::where('user_id', $userId)
            ->where('notification_id', $notification_id)
            ->with('notification')
            ->firstOrFail();

        // Tandai sebagai telah dibaca
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

        // Hapus notifikasi dari tabel `notification_recipients`
        NotificationRecipient::where('user_id', $userId)
            ->where('notification_id', $notification_id)
            ->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Menghapus semua notifikasi user.
     */
    public function clearAll()
    {
        $userId = Auth::id();

        // Hapus semua notifikasi dari tabel `notification_recipients`
        NotificationRecipient::where('user_id', $userId)->delete();

        return back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }
}