<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            [
                'bank_name' => 'Bank Mandiri',
                'account_number' => '1150099996664',
                'account_name' => 'PT Karya Prima Usahatama',
            ],
            [
                'bank_name' => 'Bank BCA',
                'account_number' => '4017030034',
                'account_name' => 'PT Karya Prima Usahatama',
            ],
            [
                'bank_name' => 'Bank BNI',
                'account_number' => '325949846',
                'account_name' => 'PT Karya Prima Usahatama',
            ],
        ];

        foreach ($banks as $bank) {
            BankAccount::create($bank);
        }
    }
}
