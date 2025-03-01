<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManfeeDocument;
use App\Models\Contracts;
use Faker\Factory as Faker;

class ManfeeDocumentSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $contracts = Contracts::pluck('id')->toArray();
        $monthRoman = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        $year = date('Y');

        $lastNumber = ManfeeDocument::max('letter_number');
        preg_match('/^(\d{6})/', $lastNumber, $matches);
        $lastNumeric = $matches[1] ?? '000100';

        for ($i = 0; $i < 10; $i++) {
            $nextNumber = intval($lastNumeric) + (($i + 1) * 10);
            $month = $monthRoman[rand(1, 12)];

            $letterNumber = sprintf("%06d/MF/KEU/KPU/SOL/%s/%s", $nextNumber, $month, $year);
            $invoiceNumber = sprintf("%06d/MF/KW/KPU/SOL/%s/%s", $nextNumber, $month, $year);
            $receiptNumber = sprintf("%06d/MF/INV/KPU/SOL/%s/%s", $nextNumber, $month, $year);

            ManfeeDocument::create([
                'contract_id' => $faker->randomElement($contracts),
                'invoice_number' => $invoiceNumber,
                'receipt_number' => $receiptNumber,
                'letter_number' => $letterNumber,
                'manfee_bill' => $faker->randomFloat(2, 1000, 10000),
                'period' => '14',
                'letter_subject' => $faker->sentence(3),
                'category' => 'management_fee',
                'status' => 0,
                'last_reviewers' => $faker->name,
                'is_active' => true,
                'created_by' => 1,
            ]);
        }

        $this->command->info("✅ Berhasil menambahkan 10 data Management Fee.");
    }
}
