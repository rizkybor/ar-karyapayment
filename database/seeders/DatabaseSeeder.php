<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            DashboardTableSeeder::class,
            UserSeeder::class,
            PermissionSeeder::class,
            MasterDataSeeder::class,
            ContractInvoiceSeeder::class,
            BankAccountSeeder::class,
            MasterBillTypeSeeder::class,
            NonManfeeDocumentSeeder::class,
            ManfeeDocumentSeeder::class,
            ContractCategorySeeder::class,
            NationalDaySeeder::class
        ]);
    }
}
