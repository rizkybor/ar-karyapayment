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

            $data[] = [
                'contract_id'    => $contract_id,
                'invoice_number' => 'INV-' . Str::upper(Str::random(10)),
                'receipt_number' => 'REC-' . Str::upper(Str::random(10)),
                'letter_number'  => 'LTR-' . Str::upper(Str::random(10)),
                'manfee_bill' => $faker->randomFloat(2, 1000, 10000),
                'period'         => '14',
                'letter_subject' => 'Tagihan Pembayaran ' . strtoupper(Str::random(5)),
                'category'       => 'management_fee',
                'status'         => 0,
                'last_reviewers' => null,
                'is_active'      => true,
                'created_by'     => $created_by,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ];
        }

        // Insert data ke database dengan batch untuk performa lebih baik
        DB::table('manfee_documents')->insert($data);

        $this->command->info("✅ Berhasil menambahkan 4 data Management Fee.");
    }
}
