<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractCategory;

class ContractCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Surat Perjanjian Kerja (SPK)', 'Perjanjian', 'Purchase Order', 'Berita Acara Kesepakatan'];

        foreach ($categories as $name) {
            ContractCategory::create(['name' => $name]);
        }
    }
}