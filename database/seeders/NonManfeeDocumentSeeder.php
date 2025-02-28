<?php

namespace Database\Seeders;

use App\Models\NonManfeeDocument;
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

        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $contract_id = $contracts->random();

            $data[] = [
                'contract_id'    => $contract_id,
                'invoice_number' => 'INV-' . Str::upper(Str::random(10)),
                'receipt_number' => 'REC-' . Str::upper(Str::random(10)),
                'letter_number'  => 'LTR-' . Str::upper(Str::random(10)),
                'period'         => now()->subMonths(rand(1, 12))->format('Y-m'),
                'letter_subject' => 'Tagihan Pembayaran ' . strtoupper(Str::random(5)),
                'category'       => 'management_non_fee',
                'status'         => rand(0, 1) ? 'approved' : 'pending',
                'last_reviewers' => null,
                'is_active'      => true,
                'created_by'     => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        // Insert data ke database dengan batch untuk performa lebih baik
        DB::table('non_manfee_documents')->insert($data);

        $this->command->info("✅ Berhasil menambahkan 5000 data Management Non Fee.");
    }
}
