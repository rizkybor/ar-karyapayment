<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Contracts;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ManfeeDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua kontrak yang bertipe 'management_fee'
        $contracts = Contracts::where('type', 'management_fee')->pluck('id');

        if ($contracts->isEmpty()) {
            $this->command->warn("Tidak ada data kontrak dengan tipe 'management_fee'. Seeder dihentikan.");
            return;
        }

        // Ambil semua user yang memiliki role 'maker'
        $makers = User::where('role', 'maker')->pluck('id');

        if ($makers->isEmpty()) {
            $this->command->warn("Tidak ada user dengan role 'maker'. Seeder dihentikan.");
            return;
        }

        $data = [];
        $faker = Faker::create();

        for ($i = 1; $i <= 10; $i++) {
            $contract_id = $contracts->random();
            $created_by = $makers->random();
            $created_at = Carbon::now();

            // ✅ Set expired_at H+30 dengan waktu tetap 00:01:00
            $expired_at = $created_at->copy()->addDays(30)->setTime(0, 1, 0);

            // 🔢 Hitung nomor urut dengan kelipatan 10 dimulai dari 110
            $nomorUrut = str_pad(110 + ($i - 1) * 10, 6, '0', STR_PAD_LEFT);

            // 🗓️ Ambil bulan romawi dan tahun
            $bulanRomawi = [
                'I',
                'II',
                'III',
                'IV',
                'V',
                'VI',
                'VII',
                'VIII',
                'IX',
                'X',
                'XI',
                'XII'
            ];

            $bulan = $bulanRomawi[(int) $created_at->format('m') - 1];
            $tahun = $created_at->format('Y');

            // 🧾 Format nomor dokumen
            $invoice_number = "$nomorUrut/MF/INV/KPU/SOL/$bulan/$tahun";
            $receipt_number = "$nomorUrut/MF/KW/KPU/SOL/$bulan/$tahun";
            $letter_number  = "$nomorUrut/MF/KEU/KPU/SOL/$bulan/$tahun";

            $data[] = [
                'contract_id'    => $contract_id,
                'invoice_number' => $invoice_number,
                'receipt_number' => $receipt_number,
                'letter_number'  => $letter_number,
                'manfee_bill'    => $faker->randomFloat(2, 1000, 10000),
                'period'         => '14',
                'letter_subject' => 'Tagihan Pembayaran ' . strtoupper(Str::random(5)),
                'category'       => 'management_fee',
                'status'         => 0,
                'status_print'      => false,
                'reference_document'=> null,
                'reason_rejected' => '',
                'path_rejected' => '',
                'last_reviewers' => null,
                'is_active'      => true,
                'created_by'     => $created_by,
                'created_at'     => $created_at,
                'updated_at'     => $created_at,
                'expired_at'     => $expired_at,
            ];
        }

        // Insert data ke database dengan batch untuk performa lebih baik
        DB::table('manfee_documents')->insert($data);

        $this->command->info("✅ Berhasil menambahkan 10 data Management Fee dengan expired_at (H+30 pukul 00:01:00).");
    }
}
