<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Role
        $role_super_admin = Role::updateOrCreate(
            [
                'name' => 'super_admin',
            ],
            ['name' => 'super_admin']
        );

        $role_maker = Role::updateOrCreate(
            [
                'name' => 'maker',
            ],
            ['name' => 'maker']
        );

        $role_kadiv = Role::updateOrCreate(
            [
                'name' => 'kadiv',
            ],
            ['name' => 'kadiv']
        );

        $role_bendahara = Role::updateOrCreate(
            [
                'name' => 'bendahara',
            ],
            ['name' => 'bendahara']
        );

        $role_manager_anggaran = Role::updateOrCreate(
            [
                'name' => 'manager_anggaran',
            ],
            ['name' => 'manager_anggaran']
        );

        $role_direktur_keuangan = Role::updateOrCreate(
            [
                'name' => 'direktur_keuangan',
            ],
            ['name' => 'direktur_keuangan']
        );

        $role_pajak = Role::updateOrCreate(
            [
                'name' => 'pajak',
            ],
            ['name' => 'pajak']
        );

        // Permission

        $dashboard = Permission::updateOrCreate(
            [
                'name' => 'view_dashboard',
            ],
            ['name' => 'view_dashboard']
        );

        // Assign Permission
        $role_maker->givePermissionTo($dashboard);
        $role_kadiv->givePermissionTo($dashboard);
        $role_bendahara->givePermissionTo($dashboard);
        $role_manager_anggaran->givePermissionTo($dashboard);
        $role_direktur_keuangan->givePermissionTo($dashboard);
        $role_pajak->givePermissionTo($dashboard);

        // Assign User
        $user_super_admin = User::where('role', 'super_admin')->first();
        $user_super_admin->assignRole('super_admin');

        $userMaker = User::where('role', 'maker')->first();
        $userMaker->assignRole('maker');

        $userKadiv = User::where('role', 'kadiv')->first();
        $userKadiv->assignRole('kadiv');

        $userBendahara = User::where('role', 'bendahara')->first();
        $userBendahara->assignRole('bendahara');

        $user_manager_anggaran = User::where('role', 'manager_anggaran')->first();
        $user_manager_anggaran->assignRole('manager_anggaran');

        $user_direktur_keuangan = User::where('role', 'direktur_keuangan')->first();
        $user_direktur_keuangan->assignRole('direktur_keuangan');

        $userPajak = User::where('role', 'pajak')->first();
        $userPajak->assignRole('pajak');
    }
}
