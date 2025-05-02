<?php

namespace Database\Seeders;

use App\Models\Contracts;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ContractInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['SPK', 'Perjanjian', 'Purchase Order', 'Berita Acara Kesepakatan'];

        $now = Carbon::now();

        $data = [
            [
                'contract_number' => 'ADSAKDSA23',
                'employee_name' => 'PT. Nusanida',
                'value' => 12500000,
                'start_date' => $now->copy()->subMonths(3),
                'end_date' => $now->copy()->addMonths(9),
            ],
            [
                'contract_number' => 'ADSAKDSA24',
                'employee_name' => 'PT. Telkom',
                'value' => 15000000,
                'start_date' => $now->copy()->subMonths(6),
                'end_date' => $now->copy()->addMonths(6),
            ],
            [
                'contract_number' => 'ADSAKDSA25',
                'employee_name' => 'PT. Indo Jaya',
                'value' => 20000000,
                'start_date' => $now->copy()->subYear(),
                'end_date' => $now->copy()->addMonths(3),
            ],
            [
                'contract_number' => 'ADSAKDSA26',
                'employee_name' => 'PT. Pasific',
                'value' => 50000000,
                'start_date' => $now->copy()->subYear(),
                'end_date' => $now->copy()->addMonths(3),
            ],
            [
                'contract_number' => 'ADSAKDSA27',
                'employee_name' => 'PT. Indo Jaya Maritim',
                'value' => 50000000,
                'start_date' => $now->copy()->subYear(),
                'end_date' => $now->copy()->addMonths(3),
            ],
            [
                'contract_number' => 'ADSAKDSA28',
                'employee_name' => 'PT. Taffware Indo',
                'value' => 50000000,
                'start_date' => $now->copy()->subYear(),
                'end_date' => $now->copy()->addMonths(3),
            ],
        ];

        foreach ($data as &$item) {
            $startDate = $item['start_date'];
            $item['contract_date'] = $startDate->copy()->subDays(rand(0, 10)); // ⬅️ tidak lebih dari start_date
            $item['type'] = in_array($item['contract_number'], ['ADSAKDSA24', 'ADSAKDSA25']) ? 'non_management_fee' : 'management_fee';
            $item['path'] = 'awdkasdk12.png';
            $item['address'] = 'Jl. Contoh Alamat No. ' . rand(1, 50);
            $item['work_unit'] = 'keuangan';
            $item['status'] = null;
            $item['title'] = 'Judul Kontrak untuk ' . $item['employee_name'];
            $item['category'] = $categories[array_rand($categories)];
            $item['created_at'] = now();
            $item['updated_at'] = now();
        }

        Contracts::insert($data);
    }
}