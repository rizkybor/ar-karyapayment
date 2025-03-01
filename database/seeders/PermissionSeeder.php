<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Jalankan seeder permission dan role.
     */
    public function run(): void
    {
        // 🔹 Ambil semua role unik dari `users` di database
        $roles = User::distinct()->pluck('role')->filter()->toArray(); // Pastikan tidak null

        // 🔹 Buatkan role berdasarkan daftar user
        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role]);
        }

        // 🔹 Definisikan permission (bisa ditambah sesuai kebutuhan)
        $permissions = [
            'view_dashboard',
            'manage_users',
            'manage_transactions',
        ];

        // 🔹 Buatkan permission
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }

        // 🔹 Berikan permission ke semua role yang ada
        foreach ($roles as $role) {
            $roleModel = Role::where('name', $role)->first();
            if ($roleModel) {
                $roleModel->givePermissionTo('view_dashboard'); // Default permission
            }
        }

        // 🔹 Assign role ke user berdasarkan data di database
        $users = User::all();

        foreach ($users as $user) {
            if ($user->role) {
                $user->assignRole($user->role);
            }
        }
    }
}