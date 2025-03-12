<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Contracts;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NonManfeeDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua kontrak yang bertipe 'non_management_fee'
        $contracts = Contracts::where('type', 'non_management_fee')->pluck('id');

        if ($contracts->isEmpty()) {
            $this->command->warn("Tidak ada data kontrak dengan tipe 'non_management_fee'. Seeder dihentikan.");
            return;
        }

        // Ambil semua user yang memiliki role 'maker'
        $makers = User::where('role', 'maker')->pluck('id');

        if ($makers->isEmpty()) {
            $this->command->warn("Tidak ada user dengan role 'maker'. Seeder dihentikan.");
            return;
        }

        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $contract_id = $contracts->random();
            $created_by = $makers->random();
            $created_at = Carbon::now();

            // ✅ Set expired_at H+30 dengan waktu tetap 00:01:00
            $expired_at = $created_at->copy()->addDays(30)->setTime(0, 1, 0);

            $data[] = [
                'contract_id'    => $contract_id,
                'invoice_number' => 'INV-' . Str::upper(Str::random(10)),
                'receipt_number' => 'REC-' . Str::upper(Str::random(10)),
                'letter_number'  => 'LTR-' . Str::upper(Str::random(10)),
                'period'         => '14',
                'letter_subject' => 'Tagihan Pembayaran ' . strtoupper(Str::random(5)),
                'category'       => 'management_non_fee',
                'status'         => 0,
                'last_reviewers' => null,
                'is_active'      => true,
                'created_by'     => $created_by,
                'created_at'     => $created_at,
                'updated_at'     => $created_at,
                'expired_at'     => $expired_at, // ✅ Waktu 00:01:00
            ];
        }

        // Insert data ke database dengan batch untuk performa lebih baik
        DB::table('non_manfee_documents')->insert($data);

        $this->command->info("✅ Berhasil menambahkan 10 data Non Management Fee dengan expired_at (H+30 pukul 00:01:00).");
    }
}