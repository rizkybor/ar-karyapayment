<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\User;

class NotificationRecipientSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('notification_recipients')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil semua user untuk dijadikan penerima notifikasi
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->info('Tidak ada user ditemukan. Seeder dibatalkan.');
            return;
        }

        // Ambil semua notifikasi
        $notifications = Notification::all();
        if ($notifications->isEmpty()) {
            $this->command->info('Tidak ada notifikasi ditemukan. Seeder dibatalkan.');
            return;
        }

        $recipients = [];

        foreach ($notifications as $notification) {
            foreach ($users as $user) {
                $recipients[] = [
                    'id' => Str::uuid(),
                    'notification_id' => $notification->id,
                    'user_id' => $user->id, // Pastikan user ada di database
                    'read_at' => null, // Semua unread saat awal
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        NotificationRecipient::insert($recipients);
        $this->command->info('Seeder NotificationRecipientSeeder berhasil dijalankan!');
    }
}