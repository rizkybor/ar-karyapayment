<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     */
    public function run(): void
    {
        // Ambil ID user pertama (sesuaikan jika perlu)
        $userId = DB::table('users')->first()->id ?? 1;

        // Notifikasi dummy untuk user tersebut
        $notifications = [
            [
                'id' => Str::uuid(),
                'type' => 'App\Notifications\InvoiceApprovalNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'message' => 'Invoice #001 telah disetujui oleh Manager Keuangan.',
                    'document_id' => 1,
                    'status' => 'approved'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(10),
                'updated_at' => Carbon::now()->subMinutes(10),
            ],
            [
                'id' => Str::uuid(),
                'type' => 'App\Notifications\InvoiceApprovalNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'message' => 'Invoice #002 menunggu persetujuan dari Anda.',
                    'document_id' => 2,
                    'status' => 'pending'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(5),
                'updated_at' => Carbon::now()->subMinutes(5),
            ],
            [
                'id' => Str::uuid(),
                'type' => 'App\Notifications\InvoiceApprovalNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'message' => 'Invoice #003 telah direvisi oleh Kepala Divisi.',
                    'document_id' => 3,
                    'status' => 'revised'
                ]),
                'read_at' => Carbon::now()->subMinutes(1),
                'created_at' => Carbon::now()->subMinutes(20),
                'updated_at' => Carbon::now()->subMinutes(20),
            ],
        ];

        // Masukkan data ke dalam tabel notifications
        DB::table('notifications')->insert($notifications);

        // Output ke terminal
        $this->command->info('Notification seeder berhasil dijalankan!');
    }
}