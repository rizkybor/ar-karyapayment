<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     */
    public function run(): void
    {
        // $users = [
        //     // Super Admin
        //     ['name' => 'Super Admin', 'email' => 'superadmin@example.com', 'role' => 'super_admin', 'department' => null, 'position' => null],

        //     // Maker Users
        //     ['name' => 'Maker 1', 'email' => 'maker1@example.com', 'role' => 'maker', 'department' => 'Departmen SDM', 'position' => 'Staff'],
        //     ['name' => 'Maker 2', 'email' => 'maker2@example.com', 'role' => 'maker', 'department' => 'Departmen Pengadaan dan Administrasi Umum', 'position' => 'Staff'],

        //     // Kepala Divisi (Kadiv)
        //     ['name' => 'Kadiv Operasi', 'email' => 'kadiv.operasi@example.com', 'role' => 'kadiv', 'department' => 'Departmen Operasi', 'position' => 'Kepala Departemen'],
        //     ['name' => 'Kadiv Keuangan', 'email' => 'kadiv.keuangan@example.com', 'role' => 'kadiv', 'department' => 'Departmen Keuangan', 'position' => 'Kepala Departemen'],
        //     ['name' => 'Kadiv Pengadaan', 'email' => 'kadiv.pengadaan@example.com', 'role' => 'kadiv', 'department' => 'Departmen Pengadaan dan Administrasi Umum', 'position' => 'Kepala Departemen'],
        //     ['name' => 'Kadiv SDM', 'email' => 'kadiv.sdm@example.com', 'role' => 'kadiv', 'department' => 'Departmen SDM', 'position' => 'Kepala Departemen'],

        //     // Perbendaharaan
        //     ['name' => 'Perbendaharaan User', 'email' => 'perbendaharaan@example.com', 'role' => 'perbendaharaan', 'department' => 'Departmen Keuangan', 'position' => 'Perbendaharaan'],

        //     // Pajak
        //     ['name' => 'Pajak User', 'email' => 'pajak@example.com', 'role' => 'pajak', 'department' => 'Departmen Keuangan', 'position' => 'Pajak'],

        //     // Manager Anggaran
        //     ['name' => 'Manager Anggaran User', 'email' => 'mgr.anggaran@example.com', 'role' => 'manager_anggaran', 'department' => 'Departmen Operasi', 'position' => 'Manager Anggaran'],
            
        //     // Direktur Utama
        //     ['name' => 'Direktur Keuangan User', 'email' => 'dirut.keuangan@example.com', 'role' => 'direktur_keuangan', 'department' => 'Departmen Operasi', 'position' => 'Direktur Keuangan'],

        // ];

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'nip' => '001',
                'department' => null,
                'position' => null,
                'role' => 'super_admin',
                'employee_status' => 'Organik',
                'gender' => 'male',
                'identity_number' => '001'
            ],
            [
                'name' => 'Rizky Ajie Kurniawan',
                'email' => 'rizkyak994@gmail.com',
                'nip' => '002',
                'department' => 'Departemen SDM & Umum',
                'position' => 'Staff',
                'role' => 'maker',
                'employee_status' => 'PKWT',
                'gender' => 'male',
                'identity_number' => '0000000002'
            ],
            [
                'name' => 'Farhan Hafizh',
                'email' => 'farhanhafizh770@gmail.com',
                'nip' => '003',
                'department' => 'Departemen Komersial/CNG',
                'position' => 'Staff',
                'role' => 'maker',
                'employee_status' => 'PKWT',
                'gender' => 'male',
                'identity_number' => '000000003'
            ],
            [
                'name' => 'RIKY OKFIANDI',
                'email' => 'rokfiandi@gmail.com',
                'nip' => '1160026',
                'department' => 'Departemen SDM & Umum',
                'position' => 'Supervisor',
                'role' => 'maker',
                'employee_status' => 'PKWT',
                'gender' => 'male',
                'identity_number' => '3603171710840005'
            ],
            [
                'name' => 'MUHAMMAD RAFIUDIN',
                'email' => 'apitrafi8@gmail.com',
                'nip' => '01160009',
                'department' => 'Departemen Operasional',
                'position' => 'Supervisor',
                'role' => 'maker',
                'employee_status' => 'Organik',
                'gender' => 'male',
                'identity_number' => '3276051611840016'
            ],
            [
                'name' => 'HERIYANTI',
                'email' => 'heriyantidaniyyala7791@gmail.com',
                'nip' => '01160017',
                'department' => 'Departemen Operasional',
                'position' => 'Supervisor',
                'role' => 'maker',
                'employee_status' => 'Organik',
                'gender' => 'female',
                'identity_number' => '3671074810770003'
            ],
            [
                'name' => 'REINA ANJAR KUSUMA WULANDARI',
                'email' => 'putrip72@gmail.com',
                'nip' => '01160025',
                'department' => 'Departemen Operasional',
                'position' => 'Staff Admin',
                'role' => 'maker',
                'employee_status' => 'Organik',
                'gender' => 'female',
                'identity_number' => '3201295211900001'
            ],
            [
                'name' => 'SUPRIYATIN',
                'email' => 'Attinsupriyatin6@gmail.com',
                'nip' => '01160027',
                'department' => 'Departemen Operasional',
                'position' => 'Supervisor',
                'role' => 'maker',
                'employee_status' => 'Organik',
                'gender' => 'female',
                'identity_number' => '3174032109760003'
            ],
            [
                'name' => 'AZIS JALALUDIN',
                'email' => 'azis.jalaludin8@gmail.com',
                'nip' => '02191717',
                'department' => 'Departemen Operasional',
                'position' => 'Staff Admin',
                'role' => 'maker',
                'employee_status' => 'Organik',
                'gender' => 'male',
                'identity_number' => '3202280801880001'
            ],
            [
                'name' => 'M AGUS TIAN',
                'email' => 'Muhamad14agustian@gmail.com',
                'nip' => '05161070',
                'department' => 'Departemen Operasional',
                'position' => 'Staff Admin',
                'role' => 'maker',
                'employee_status' => 'Karyawan',
                'gender' => 'male',
                'identity_number' => '3603281508900004'
            ],
            [
                'name' => 'RULI MEIRANI',
                'email' => 'Rulimeirani09.@gmail.com',
                'nip' => '08173013',
                'department' => 'Departemen Operasional',
                'position' => 'Staff',
                'role' => 'maker',
                'employee_status' => 'Non Organik',
                'gender' => 'male',
                'identity_number' => '3174051705760007'
            ],
            [
                'name' => 'M. ATIP',
                'email' => 'muhammadatipmsj@gmail.com',
                'nip' => '11224464',
                'department' => 'Departemen Operasional',
                'position' => 'Supervisor',
                'role' => 'maker',
                'employee_status' => 'Organik',
                'gender' => 'male',
                'identity_number' => '3674060803800001'
            ],
            [
                'name' => 'PERMATA ALPANJA BHIMA',
                'email' => 'alpanja.permata@gmail.com',
                'nip' => '06235196',
                'department' => 'Departemen Komersial/CNG',
                'position' => ' Administrasi CNG',
                'role' => 'maker',
                'employee_status' => 'Kontrak',
                'gender' => 'female',
                'identity_number' => '3674066105970005'
            ],
            [
                'name' => 'ATIK WIDIANTI',
                'email' => 'Atikwidianti20@gmail.com',
                'nip' => '02193009',
                'department' => 'Departemen Ketehnikan',
                'position' => 'Administrasi',
                'role' => 'maker',
                'employee_status' => 'PKWT',
                'gender' => 'female',
                'identity_number' => '3301067108940001'
            ],
            [
                'name' => 'BAGUS ENGGAL PRAKOSO',
                'email' => 'bagusenggalprakoso.work@.gmail.com',
                'nip' => '01245175',
                'department' => 'Departemen HSSE',
                'position' => 'HSSE Officer',
                'role' => 'maker',
                'employee_status' => 'PKWT',
                'gender' => 'male',
                'identity_number' => '3603220603970003'
            ],
            [
                'name' => 'PERDI MAULANA',
                'email' => 'perdimaulana15@gmail.com',
                'nip' => '0116000',
                'department' => 'Departemen SDM & Umum',
                'position' => 'Manager SDM & Umum',
                'role' => 'kadiv',
                'employee_status' => 'PKWT',
                'gender' => 'male',
                'identity_number' => '174071508840006'
            ],
            [
                'name' => 'PERDI MAULANA',
                'email' => 'perdimaulana.ops@gmail.com',
                'nip' => '0116000',
                'department' => 'Departemen Operasional',
                'position' => 'Manager Operasional',
                'role' => 'kadiv',
                'employee_status' => 'PKWT',
                'gender' => 'male',
                'identity_number' => '174071508840006'
            ],
            [
                'name' => 'MOH. ZAMZAMI',
                'email' => 'zamy.kpu2016@gmail.com',
                'nip' => '01160022',
                'department' => 'Departemen Ketehnikan',
                'position' => 'Manager Ketehnikan',
                'role' => 'kadiv',
                'employee_status' => 'PKWT',
                'gender' => 'male',
                'identity_number' => '3603170405790019'
            ],
            [
                'name' => 'DIDIT AGUSTIAWAN',
                'email' => 'diditagustiawan1@gmail.com',
                'nip' => '01160016',
                'department' => 'Departemen HSSE',
                'position' => 'Manager HSSE',
                'role' => 'kadiv',
                'employee_status' => 'Organik',
                'gender' => 'male',
                'identity_number' => '3674060208730004'
            ],
            [
                'name' => 'HERVIAN BAGUS SAPUTRA',
                'email' => 'commercial@pt-kpusahatama.com',
                'nip' => '11203397',
                'department' => 'Departemen Komersial/CNG',
                'position' => 'Manager Komersial / CNG',
                'role' => 'kadiv',
                'employee_status' => 'Organik',
                'gender' => 'male',
                'identity_number' => '3175033108900005'
            ],
            [
                'name' => 'HAFIANA SYAFITRI',
                'email' => 'hafiana.sft@gmail.com',
                'nip' => '07224333',
                'department' => 'Departemen Pajak',
                'position' => 'Sr Staff Pajak',
                'role' => 'pajak',
                'employee_status' => 'PKWT',
                'gender' => 'female',
                'identity_number' => '3175094301970004'
            ],
            [
                'name' => 'FATIKHA PEVILIAN NUR AGUSTIN',
                'email' => 'pevilian.ags@gmail.com',
                'nip' => '05202961',
                'department' => 'Departemen Keuangan',
                'position' => 'Staff Anggaran & Perbendaharaan',
                'role' => 'perbendaharaan',
                'employee_status' => 'Organik',
                'gender' => 'female',
                'identity_number' => '3174034308960005'
            ],
            [
                'name' => 'MUTIARA ALPANJA BHIMA',
                'email' => 'Mutiara.ab22@gmail.com',
                'nip' => '04170024',
                'department' => 'Departemen Keuangan',
                'position' => 'Manager Anggaran & Perbendaharaan',
                'role' => 'manager_anggaran',
                'employee_status' => 'Organik',
                'gender' => 'female',
                'identity_number' => '3674066209880003'
            ],
            [
                'name' => 'Sutaryo',
                'email' => 'sutaryo@example.com',
                'nip' => '99999999',
                'department' => 'Departemen Keuangan',
                'position' => 'Direktur Keuangan',
                'role' => 'direktur_keuangan',
                'employee_status' => 'Organik',
                'gender' => 'male',
                'identity_number' => '999999999999999'
            ],
        ];

        // foreach ($users as $user) {
        //     User::create([
        //         'name' => $user['name'],
        //         'email' => $user['email'],
        //         'password' => Hash::make('P@ssw0rd'),
        //         'nip' => rand(10000000, 99999999),
        //         'department' => $user['department'],
        //         'position' => $user['position'],
        //         'role' => $user['role'],
        //         'employee_status' => 'permanent',
        //         'gender' => 'male',
        //         'identity_number' => rand(100000000, 999999999),
        //         'signature' => null,
        //     ]);
        // }

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('P@ssw0rd'),
                'nip' => $user['nip'],
                'department' => $user['department'],
                'position' => $user['position'],
                'role' => $user['role'],
                'employee_status' => 'permanent',
                'gender' => $user['gender'],
                'identity_number' => $user['identity_number'],
                'signature' => null,
            ]);
        }
    }
}