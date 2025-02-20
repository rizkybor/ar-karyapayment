<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\MasterType;
use App\Models\MasterBillType;
use App\Models\MasterWorkUnit;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterType::insert([
            ['type' => 'management_fee'],
            ['type' => 'non_management_fee'],
        ]);

        MasterBillType::insert([
            ['bill_type' => 'gaji'],
            ['bill_type' => 'sppd'],
        ]);

        MasterWorkUnit::insert([
            ['work_unit' => 'keuangan'],
            ['work_unit' => 'sumber_daya_manusia'],
            ['work_unit' => 'teknologi_informasi'],
            ['work_unit' => 'operasional'],
        ]);
    }
}
