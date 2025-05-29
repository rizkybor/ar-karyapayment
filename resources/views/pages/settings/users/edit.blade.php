<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1 flex justify-between">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Akun</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Ubah informasi pengguna yang terdaftar.
                    </p>

                    @if ($errors->any())
                        <div class="md:col-span-3 mt-4">
                            <div
                                class="rounded-md bg-red-50 dark:bg-red-900 p-4 border border-red-300 dark:border-red-700">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-600 dark:text-red-300" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.707-5.293a1 1 0 011.414 0l.293.293.293-.293a1 1 0 111.414 1.414l-.293.293.293.293a1 1 0 11-1.414 1.414l-.293-.293-.293.293a1 1 0 11-1.414-1.414l.293-.293-.293-.293a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Terdapat
                                            kesalahan input:</h3>
                                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                            <ul class="list-disc pl-5 space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>



            <div class="mt-5 md:mt-0 md:col-span-2">
                <form method="POST" action="{{ route('list_users.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="grid grid-cols-1 gap-y-6">
                            <div>
                                <x-label for="name">Full Name <span class="text-red-500">*</span></x-label>
                                <x-input id="name" type="text" name="name"
                                    class="mt-1 block w-full min-h-[40px]" value="{{ old('name', $user->name) }}"
                                    required autofocus />
                            </div>

                            <div>
                                <x-label for="nip">NIP <span class="text-red-500">*</span></x-label>
                                <x-input id="nip" type="text" name="nip"
                                    value="{{ old('nip', $user->nip) }}" required oninput="limitNIPLength(this)" />
                            </div>

                            <div>
                                <x-label for="email">Email Address <span class="text-red-500">*</span></x-label>
                                <x-input id="email" type="email" name="email"
                                    value="{{ old('email', $user->email) }}" required />
                            </div>

                            <div>
                                <x-label for="role">Role <span class="text-red-500">*</span></x-label>
                                <select id="role" name="role"
                                    class="mt-1 block w-full form-select rounded-md border-gray-300 shadow-sm" required>
                                    <option value="">Pilih Role</option>
                                    @foreach ($roles as $role)
                                        @php
                                            $isUsed = in_array($role->name, $usedRoles);
                                            $isCurrent = $user->role === $role->name;
                                        @endphp
                                        <option value="{{ $role->name }}" {{ $isCurrent ? 'selected' : '' }}
                                            {{ $isUsed && !$isCurrent ? 'disabled' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-label for="department">Department <span class="text-red-500">*</span></x-label>
                                <select id="department" name="department"
                                    class="mt-1 block w-full form-select rounded-md border-gray-300 shadow-sm" required>
                                    <option value="">Pilih Department</option>
                                    @foreach ($departments as $dept)
                                        @php
                                            $isKadiv = $user->role === 'kadiv';
                                            $disabledForKadiv =
                                                in_array($dept, $kadivDepartments) && $user->department !== $dept;
                                            $hiddenForKadiv = $isKadiv && in_array($dept, $excludedKadivDepartments);
                                        @endphp
                                        @if (!$hiddenForKadiv)
                                            <option value="{{ $dept }}"
                                                {{ $user->department === $dept ? 'selected' : '' }}
                                                {{ $disabledForKadiv ? 'disabled' : '' }}>
                                                {{ $dept }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-label for="position">Position <span class="text-red-500">*</span></x-label>
                                <x-input id="position" type="text" name="position"
                                    value="{{ old('position', $user->position) }}" required />
                            </div>

                            <div>
                                <x-label for="employee_status">Employee Status <span
                                        class="text-red-500">*</span></x-label>
                                <x-input id="employee_status" type="text" name="employee_status"
                                    value="{{ old('employee_status', $user->employee_status) }}" required />
                            </div>

                            <div>
                                <x-label for="gender">Gender <span class="text-red-500">*</span></x-label>
                                <div class="mt-1 space-y-2">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="gender" value="male" class="form-radio h-4 w-4"
                                            {{ $user->gender === 'male' ? 'checked' : '' }} required />
                                        <span class="ml-2">Pria</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="gender" value="female" class="form-radio h-4 w-4"
                                            {{ $user->gender === 'female' ? 'checked' : '' }} required />
                                        <span class="ml-2">Wanita</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <x-label for="identity_number">Identity Number <span
                                        class="text-red-500">*</span></x-label>
                                <x-input id="identity_number" type="text" name="identity_number"
                                    value="{{ old('identity_number', $user->identity_number) }}" required />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-2">
                            <x-secondary-button type="button"
                                onclick="window.location='{{ route('list_users') }}'">Batal</x-secondary-button>
                            <x-button-action color="violet" type="submit">Simpan Perubahan</x-button-action>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function limitNIPLength(input) {
                if (input.value.length > 8) {
                    input.value = input.value.slice(0, 8);
                }
            }
        </script>

        <script>
            const roleSelect = document.getElementById('role');
            const departmentSelect = document.getElementById('department');

            const kadivDepartments = @json($kadivDepartments);
            const currentRole = '{{ $user->role }}';
            const currentDepartment = '{{ $user->department }}';

            function updateDepartmentOptions() {
                const selectedRole = roleSelect.value;

                for (let i = 0; i < departmentSelect.options.length; i++) {
                    const option = departmentSelect.options[i];
                    const dept = option.value;

                    option.hidden = false;
                    option.disabled = false;

                    // Jika role = pajak → hanya tampilkan Departemen Pajak
                    if (selectedRole === 'pajak' && dept !== 'Departemen Pajak') {
                        option.hidden = true;
                        if (currentDepartment !== dept) option.disabled = true;
                    }

                    // Jika role = pajak → hanya tampilkan Departemen Pajak
                    if (selectedRole === 'manager_anggaran' && dept !== 'Departemen Keuangan' || selectedRole ===
                        'direktur_keuangan' && dept !== 'Departemen Keuangan') {
                        option.hidden = true;
                        if (currentDepartment !== dept) option.disabled = true;
                    }

                    // Jika role = perbendaharaan → hanya tampilkan Departemen Keuangan
                    else if (selectedRole === 'perbendaharaan' && dept !== 'Departemen Keuangan') {
                        option.hidden = true;
                        if (currentDepartment !== dept) option.disabled = true;
                    }

                    // Jika role = kadiv atau maker → sembunyikan Pajak & Keuangan
                    else if ((selectedRole === 'kadiv' || selectedRole === 'maker') &&
                        (dept === 'Departemen Pajak' || dept === 'Departemen Keuangan')) {
                        option.hidden = true;
                        if (currentDepartment !== dept) option.disabled = true;
                    }

                    // Jika role = kadiv → disable department yang sudah ada kadiv-nya
                    else if (selectedRole === 'kadiv' && kadivDepartments.includes(dept)) {
                        if (dept !== currentDepartment) option.disabled = true;
                    }
                }
            }

            roleSelect.addEventListener('change', updateDepartmentOptions);
            document.addEventListener('DOMContentLoaded', updateDepartmentOptions);
        </script>
    </div>
</x-app-layout>
