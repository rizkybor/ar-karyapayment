<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Maker User', 'email' => 'maker@example.com', 'role' => 'maker'],
            ['name' => 'Kadiv User', 'email' => 'kadiv@example.com', 'role' => 'kadiv'],
            ['name' => 'Bendahara User', 'email' => 'bendahara@example.com', 'role' => 'bendahara'],
            ['name' => 'Manager Anggaran', 'email' => 'manager_anggaran@example.com', 'role' => 'manager_anggaran'],
            ['name' => 'Direktur Keuangan', 'email' => 'direktur@example.com', 'role' => 'direktur_keuangan'],
            ['name' => 'Pajak User', 'email' => 'pajak@example.com', 'role' => 'pajak'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('P@ssw0rd'),
                'nip' => rand(10000000, 99999999),
                'department' => 'Finance',
                'position' => ucfirst(str_replace('_', ' ', $user['role'])),
                'role' => $user['role'],
                'employee_status' => 'permanent',
                'gender' => 'male',
                'identity_number' => rand(100000000, 999999999),
                'signature' => null,
            ]);
        }
    }
}
