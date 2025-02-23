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
        DB::table('notification_recipients')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Pastikan ada user yang bisa menerima notifikasi
        $user = User::first();
        if (!$user) {
            $this->command->info('Tidak ada user ditemukan. Seeder dibatalkan.');
            return;
        }

        $notifications = Notification::all();
        $recipients = [];

        foreach ($notifications as $notification) {
            $recipients[] = [
                'id' => Str::uuid(),
                'notification_id' => $notification->id,
                'user_id' => $user->id, // Pastikan ini ada di database
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
                'updated_at' => $notification->updated_at,
            ];
        }

        NotificationRecipient::insert($recipients);
        $this->command->info('Seeder NotificationRecipientSeeder berhasil dijalankan!');
    }
}