<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractCategory;

class ContractCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Jasa', 'Barang', 'Konsultan', 'Lainnya'];

        foreach ($categories as $name) {
            ContractCategory::create(['name' => $name]);
        }
    }
}