<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Matikan sementara foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus semua data pada tabel recipients sebelum menghapus notifications
        DB::table('notification_recipients')->delete();
        DB::table('notifications')->delete();

        // Hidupkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Buat notifikasi dummy
        $notifications = [];
        for ($i = 1; $i <= 12; $i++) {
            $notifications[] = [
                'id' => Str::uuid(),
                'type' => 'App\Notifications\InvoiceApprovalNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => 1, // Pastikan ada user dengan ID 1
                'data' => json_encode([
                    'message' => "Invoice #00$i telah disetujui.",
                    'document_id' => $i,
                    'status' => 'approved'
                ]),
                'read_at' => $i <= 8 ? Carbon::now() : null, // 4 terbaru belum dibaca
                'created_at' => Carbon::now()->subMinutes(5 * $i),
                'updated_at' => Carbon::now()->subMinutes(5 * $i),
            ];
        }

        // Masukkan ke database
        Notification::insert($notifications);

        $this->command->info('Seeder NotificationSeeder berhasil dijalankan!');
    }
}