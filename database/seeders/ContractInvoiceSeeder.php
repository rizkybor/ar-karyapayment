<?php

namespace Database\Seeders;

use App\Models\Contracts;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContractInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['SPK', 'Perjanjian', 'Purchase Order', 'Berita Acara Kesepakatan'];
        $workUnits = ['keuangan', 'sdm', 'pengadaan', 'operasional', 'teknik'];
        $types = ['management_fee', 'non_management_fee'];
        $companies = [
            'PT. Nusanida',
            'PT. Telkom',
            'PT. Indo Jaya',
            'PT. Pasific',
            'PT. Indo Jaya Maritim',
            'PT. Taffware Indo',
            'PT. Energi Prima',
            'CV. Mandiri Teknik',
            'PT. Bangun Jaya Abadi',
            'UD. Sumber Rejeki',
            'PT. Global Network',
            'CV. Cipta Karya',
            'PT. Surya Mandiri',
            'PT. Bina Karya Utama',
            'CV. Anugerah Jaya',
            'PT. Multi Artha',
            'PT. Graha Indah',
            'CV. Mitra Sejahtera',
            'PT. Nusantara Jaya',
            'PT. Adhi Karya',
            'CV. Bintang Terang'
        ];

        $contracts = [];

        for ($i = 1; $i <= 21; $i++) {
            $startDate = Carbon::now()->subMonths(rand(1, 12));
            $endDate = $startDate->copy()->addMonths(rand(3, 24));

            $contracts[] = [
                'contract_number' => 'CNTR-' . strtoupper(Str::random(3)) . $i . date('Y'),
                'employee_name' => $companies[array_rand($companies)],
                'value' => rand(10000000, 100000000),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'type' => $types[array_rand($types)],
                'path' => 'contract-' . $i . '.pdf',
                'address' => 'Jl. ' . Str::random(8) . ' No. ' . rand(1, 100) . ', ' . ['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Semarang'][array_rand(['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Semarang'])],
                'work_unit' => $workUnits[array_rand($workUnits)],
                'status' => null,
                'title' => $this->generateContractTitle(),
                'category' => $categories[array_rand($categories)],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Contracts::insert($contracts);
    }

    private function generateContractTitle(): string
    {
        $activities = [
            'Pekerjaan Revitalisasi',
            'Pengadaan Jaringan',
            'Pembangunan Gedung',
            'Penyediaan Peralatan',
            'Pengerjaan Kapal',
            'Pemeliharaan Sistem',
            'Instalasi Jaringan',
            'Renovasi Kantor',
            'Pengembangan Software',
            'Pelatihan Karyawan',
            'Audit Sistem',
            'Konsultasi Manajemen',
            'Pemasangan CCTV',
            'Perbaikan Infrastruktur',
            'Pengadaan Material',
            'Jasa Konsultansi',
            'Pembangunan Jalan',
            'Perawatan Mesin',
            'Desain Arsitektur',
            'Pengelolaan Limbah',
            'Sistem Keamanan'
        ];

        $targets = [
            'Area Kantor',
            'Gedung Utama',
            'Jaringan Fiber Optik',
            'Sistem IT',
            'Armada Laut',
            'Fasilitas Produksi',
            'Data Center',
            'Kantor Cabang',
            'Sistem Keamanan',
            'Infrastruktur Jaringan',
            'Gudang Penyimpanan',
            'Laboratorium',
            'Ruang Server',
            'Area Parkir',
            'Sistem Kelistrikan',
            'Gedung Perkantoran',
            'Pabrik Produksi',
            'Sistem Pemadam Kebakaran',
            'Ruang Meeting',
            'Area Taman',
            'Fasilitas Olahraga'
        ];

        $companies = [
            'PT PGAS Solution',
            'PT Telkom Indonesia',
            'PT Bank Mandiri',
            'PT Pertamina',
            'PT PLN',
            'PT Jasa Marga',
            'PT Adhi Karya',
            'PT Wijaya Karya',
            'PT Pembangunan Perumahan',
            'PT Astra International',
            'PT Unilever Indonesia',
            'PT Indofood',
            'PT Kalbe Farma',
            'PT Semen Indonesia',
            'PT Kereta Api Indonesia',
            'PT Garuda Indonesia',
            'PT Pelabuhan Indonesia',
            'PT Jasa Raharja',
            'PT Pos Indonesia',
            'PT Angkasa Pura',
            'PT Telkomsel'
        ];

        return $activities[array_rand($activities)] . ' ' .
            $targets[array_rand($targets)] . ' ' .
            $companies[array_rand($companies)];
    }
}
