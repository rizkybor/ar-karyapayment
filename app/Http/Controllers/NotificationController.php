<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Menampilkan daftar notifikasi user yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil notifikasi yang belum dibaca terlebih dahulu
        $unreadNotifications = $user->unreadNotifications()->latest()->get();
        $readNotifications = $user->readNotifications()->latest()->limit(10)->get(); // Batasi notifikasi lama

        return view('pages/notifications/index', compact('unreadNotifications', 'readNotifications'));
    }

    /**
     * Menampilkan detail notifikasi dan menandainya sebagai telah dibaca.
     */
    public function show($id)
    {
        $user = Auth::user();

        // Ambil notifikasi tertentu
        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        // Tandai sebagai telah dibaca jika belum dibaca
        if ($notification->unread()) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

    /**
     * Menandai semua notifikasi sebagai telah dibaca.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Menghapus satu notifikasi.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        // Cari dan hapus notifikasi
        $user->notifications()->where('id', $id)->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Menghapus semua notifikasi user.
     */
    public function clearAll()
    {
        $user = Auth::user();
        $user->notifications()->delete();

        return back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }
}