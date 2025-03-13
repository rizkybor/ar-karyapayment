<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NonManfeeDocument;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NonManfeeDocAccumulatedCostSeeder extends Seeder
{
    /**
     * Jalankan database seeder.
     */
    public function run(): void
    {
        // Ambil semua ID dari NonManfeeDocument yang telah dibuat
        $nonManfeeDocuments = NonManfeeDocument::pluck('id');

        if ($nonManfeeDocuments->isEmpty()) {
            $this->command->warn("⚠️ Tidak ada data NonManfeeDocument yang tersedia. Seeder dihentikan.");
            return;
        }

        $data = [];

        foreach ($nonManfeeDocuments as $document_id) {
            $data[] = [
                'document_id' => $document_id,
                'account' => null,
                'dpp' => '0', 
                'rate_ppn' => 0.00,
                'nilai_ppn' => 0.00,
                'total' => 0.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        // Insert batch untuk performa lebih baik
        DB::table('non_manfee_doc_accumulated_costs')->insert($data);

        $this->command->info("✅ Berhasil menambahkan data Akumulasi Biaya dengan nilai default 0 untuk setiap NonManfeeDocument.");
    }
}