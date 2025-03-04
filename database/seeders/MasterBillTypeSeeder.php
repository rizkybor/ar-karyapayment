<?php

namespace Database\Seeders;

use App\Models\Contracts;
use App\Models\MasterBillType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterBillTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua kontrak yang sudah ada di database

        MasterBillType::insert([
            [
                'contract_id' => '1',
                'bill_type' => 'Gaji',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '1',
                'bill_type' => 'SPPD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '1',
                'bill_type' => 'Lembur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '4',
                'bill_type' => 'Gaji',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '4',
                'bill_type' => 'SPPD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '4',
                'bill_type' => 'Lembur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '5',
                'bill_type' => 'SPPD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '5',
                'bill_type' => 'Lembur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '6',
                'bill_type' => 'Gaji',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '6',
                'bill_type' => 'SPPD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '6',
                'bill_type' => 'Lembur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
