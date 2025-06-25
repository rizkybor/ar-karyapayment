<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->where('role', '!=', 'super_admin') // Sembunyikan super_admin
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            })
            ->paginate(10);

        return view('pages.settings.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        // Ambil semua role KECUALI super_admin
        $roles = Role::where('name', '!=', 'super_admin')->get();

        // Role terbatas: hanya boleh satu user
        $limitedRoles = ['pajak', 'perbendaharaan', 'manager_anggaran', 'direktur_keuangan'];
        $usedRoles = User::whereIn('role', $limitedRoles)
            ->where('id', '!=', $user->id) // Abaikan user yang sedang diedit
            ->pluck('role')
            ->toArray();

        // Department yang sudah memiliki Kadiv selain user ini
        $kadivDepartments = User::where('role', 'kadiv')
            ->where('id', '!=', $user->id)
            ->pluck('department')
            ->toArray();

        // Department yang tidak bisa dipilih oleh Kadiv
        $excludedKadivDepartments = ['Department Akuntansi & Pajak', 'Department Anggaran & Perbendaharaan'];

        // Daftar semua department (bisa juga dari config)
        $departments = [
            'Department SDM & Layanan Umum',
            'Department Pengusahaan Gas & Fasilitas Pendukung',
            'Department Operasi TAD & Fasilitas Pendukung',
            'Department Tehnik',
            'Department HSSE & Legal',
            'Department Akuntansi & Pajak',
            'Department Anggaran & Perbendaharaan',
        ];

        return view('pages.settings.users.edit', compact(
            'user',
            'roles',
            'usedRoles',
            'kadivDepartments',
            'excludedKadivDepartments',
            'departments'
        ));
    }


    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'nip' => ['required', 'string', 'digits:8'],
                'department' => ['required', 'string', 'max:255'],
                'position' => ['required', 'string', 'max:255'],
                'role' => ['required', 'string', 'max:255'],
                'employee_status' => ['required', 'string', 'max:255'],
                'gender' => ['required', 'string', 'max:255'],
                'identity_number' => ['required', 'string', 'max:255'],
            ]);

            $user->update($validated);

            // Pastikan sync role juga dilakukan
            $user->syncRoles([$validated['role']]);

            return redirect()->route('list_users')->with('success', 'Data pengguna berhasil diperbarui.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();

            return redirect()->route('list_users')->with('success', 'User berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}
