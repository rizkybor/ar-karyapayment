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
        $users = [
            // Super Admin
            ['name' => 'Super Admin', 'email' => 'superadmin@example.com', 'role' => 'super_admin', 'department' => null, 'position' => null],

            // Maker Users
            ['name' => 'Maker 1', 'email' => 'maker1@example.com', 'role' => 'maker', 'department' => 'Departmen SDM', 'position' => 'Staff'],
            ['name' => 'Maker 2', 'email' => 'maker2@example.com', 'role' => 'maker', 'department' => 'Departmen Pengadaan dan Administrasi Umum', 'position' => 'Staff'],

            // Kepala Divisi (Kadiv)
            ['name' => 'Kadiv Operasi', 'email' => 'kadiv.operasi@example.com', 'role' => 'kadiv', 'department' => 'Departmen Operasi', 'position' => 'Kepala Departemen'],
            ['name' => 'Kadiv Keuangan', 'email' => 'kadiv.keuangan@example.com', 'role' => 'kadiv', 'department' => 'Departmen Keuangan', 'position' => 'Kepala Departemen'],
            ['name' => 'Kadiv Pengadaan', 'email' => 'kadiv.pengadaan@example.com', 'role' => 'kadiv', 'department' => 'Departmen Pengadaan dan Administrasi Umum', 'position' => 'Kepala Departemen'],
            ['name' => 'Kadiv SDM', 'email' => 'kadiv.sdm@example.com', 'role' => 'kadiv', 'department' => 'Departmen SDM', 'position' => 'Kepala Departemen'],

            // Pembendaharaan
            ['name' => 'Pembendaharaan User', 'email' => 'pembendaharaan@example.com', 'role' => 'pembendaharaan', 'department' => 'Departmen Keuangan', 'position' => 'Pembendaharaan'],

            // Pajak
            ['name' => 'Pajak User', 'email' => 'pajak@example.com', 'role' => 'pajak', 'department' => 'Departmen Keuangan', 'position' => 'Pajak'],

            // Manager Anggaran
            ['name' => 'Manager Anggaran User', 'email' => 'mgr.anggaran@example.com', 'role' => 'manager_anggaran', 'department' => 'Departmen Operasi', 'position' => 'Manager Anggaran'],
            
            // Direktur Utama
            ['name' => 'Direktur Keuangan User', 'email' => 'dirut.keuangan@example.com', 'role' => 'direktur_keuangan', 'department' => 'Departmen Operasi', 'position' => 'Direktur Keuangan'],

        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('P@ssw0rd'),
                'nip' => rand(10000000, 99999999),
                'department' => $user['department'],
                'position' => $user['position'],
                'role' => $user['role'],
                'employee_status' => 'permanent',
                'gender' => 'male',
                'identity_number' => rand(100000000, 999999999),
                'signature' => null,
            ]);
        }
    }
}