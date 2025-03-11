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
        
        Contracts::insert([
            [
                'contract_number' => 'ADSAKDSA23',
                'employee_name' => 'PT. Nusanida',
                'value' => 12500000,
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(9),
                'type' => 'management_fee',
                'path' => 'awdkasdk.png',
                'address' => 'Jl. Merdeka No. 10, Jakarta',
                'work_unit' => 'keuangan',
                'status' => null,
                'title' => 'Pekerjaan Revitalisasi Area Taman Belakang Kantor PT PGAS Solution Area Head Cirebon',
                'category' => $categories[array_rand($categories)],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_number' => 'ADSAKDSA24',
                'employee_name' => 'PT. Telkom',
                'value' => 15000000,
                'start_date' => Carbon::now()->subMonths(6),
                'end_date' => Carbon::now()->addMonths(6),
                'type' => 'non_management_fee',
                'path' => 'awdkasdk12.png',
                'address' => 'Jl. Sudirman No. 20, Bandung',
                'work_unit' => 'keuangan',
                'status' => null,
                'title' => 'Pekerjaan Pengadaan Jaringan Fiber Optik PT Telkom',
                'category' => $categories[array_rand($categories)],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_number' => 'ADSAKDSA25',
                'employee_name' => 'PT. Indo Jaya',
                'value' => 20000000,
                'start_date' => Carbon::now()->subYear(),
                'end_date' => Carbon::now()->addMonths(3),
                'type' => 'non_management_fee',
                'path' => 'awdkasdk12.png',
                'address' => 'Jl. Diponegoro No. 15, Surabaya',
                'work_unit' => 'keuangan',
                'status' => null,
                'title' => 'Proyek Pembangunan Gedung Kantor PT Indo Jaya',
                'category' => $categories[array_rand($categories)],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_number' => 'ADSAKDSA26',
                'employee_name' => 'PT. Pasific',
                'value' => 50000000,
                'start_date' => Carbon::now()->subYear(),
                'end_date' => Carbon::now()->addMonths(3),
                'type' => 'management_fee',
                'path' => 'awdkasdk12.png',
                'address' => 'Jl. Ansaman Raya No. 15, Surabaya',
                'work_unit' => 'keuangan',
                'status' => null,
                'title' => 'Pengadaan Peralatan Kantor PT Pasific',
                'category' => $categories[array_rand($categories)],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_number' => 'ADSAKDSA27',
                'employee_name' => 'PT. Indo Jaya Maritim',
                'value' => 50000000,
                'start_date' => Carbon::now()->subYear(),
                'end_date' => Carbon::now()->addMonths(3),
                'type' => 'management_fee',
                'path' => 'awdkasdk12.png',
                'address' => 'Jl. Rohaya, No 12',
                'work_unit' => 'keuangan',
                'status' => null,
                'title' => 'Kontrak Pengerjaan Kapal PT Indo Jaya Maritim',
                'category' => $categories[array_rand($categories)],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_number' => 'ADSAKDSA28',
                'employee_name' => 'PT. Taffware Indo',
                'value' => 50000000,
                'start_date' => Carbon::now()->subYear(),
                'end_date' => Carbon::now()->addMonths(3),
                'type' => 'management_fee',
                'path' => 'awdkasdk12.png',
                'address' => 'Jl. Jaya Energi, No 37',
                'work_unit' => 'keuangan',
                'status' => null,
                'title' => 'Penyediaan Alat Elektronik PT Taffware Indo',
                'category' => $categories[array_rand($categories)],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}