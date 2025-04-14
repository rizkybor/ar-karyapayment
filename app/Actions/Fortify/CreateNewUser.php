<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Spatie\Permission\Models\Role;



class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'integer', 'digits:8', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'department' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'employee_status' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255'],
            'identity_number' => ['required', 'string', 'max:255'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'nip' => $input['nip'],
            'department' => $input['department'],
            'position' => $input['position'],
            'role' => $input['role'],
            'employee_status' => $input['employee_status'],
            'gender' => $input['gender'],
            'identity_number' => $input['identity_number'],
        ]);

        // Give Roles

        $roleName = $input['role'];

        $role = Role::where('name', $roleName)->first();

        if ($role) {
            $user->assignRole($roleName);
            $user->givePermissionTo($role->permissions);
        }

        return $user;
    }
}
