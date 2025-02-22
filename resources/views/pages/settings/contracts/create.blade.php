<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            {{-- Judul --}}
            <div class="md:col-span-1 flex justify-between">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tambah Kontrak</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Tambahkan informasi kontrak baru, termasuk detail pihak terkait, durasi, dan persyaratan khusus.
                    </p>
                </div>
            </div>
            {{-- Judul End --}}

            {{-- Tambah Data Baru --}}
            <div class="mt-5 md:mt-0 md:col-span-2">

                <form action="{{ route('contracts.store') }}" class="max-w-3xl w-11/12 mx-auto p-6" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="grid grid-cols-1 gap-y-6">
                            <div>
                                <x-label for="contract_number" value="{{ __('Nomer Kontrak') }}" />
                                <x-input id="contract_number" type="text" name="contract_number"
                                    class="mt-1 block w-full min-h-[40px]" placeholder="Masukkan nomer kontrak"
                                    wire:model.live="state.contract_number" required autocomplete="contract_number"
                                    maxlength="10" />
                                <x-input-error for="contract_number" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="employee_name" value="{{ __('Nama Perusahaan') }}" />
                                <x-input id="employee_name" type="text" name="employee_name"
                                    class="mt-1 block w-full min-h-[40px]" placeholder="Masukkan nama perusahaan"
                                    wire:model.live="state.employee_name" required autocomplete="employee_name" />
                                <x-input-error for="employee_name" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="value" value="{{ __('Nilai Kontrak') }}" />
                                <x-input id="value" type="text" name="formatted_value"
                                    class="mt-1 block w-full min-h-[40px]" placeholder="Masukkan Nilai Kontrak"
                                    oninput="formatRupiah(this)" onblur="removeFormat(this)" required />
                                <x-input-error for="value" class="mt-2" />
                            </div>

                            <!-- Input Hidden untuk Database -->
                            <input type="hidden" name="value" id="value_hidden">


                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-label for="start_date" value="{{ __('Tanggal Mulai') }}" />
                                    <x-input id="start_date" type="date" name="start_date"
                                        class="mt-1 block w-full min-h-[40px]" required autocomplete="start_date"
                                        oninput="setMinEndDate()" />
                                    <x-input-error for="start_date" class="mt-2" />
                                </div>

                                <div>
                                    <x-label for="end_date" value="{{ __('Tanggal Selesai') }}" />
                                    <x-input id="end_date" type="date" name="end_date"
                                        class="mt-1 block w-full min-h-[40px]" required autocomplete="end_date" />
                                    <x-input-error for="end_date" class="mt-2" />
                                </div>

                            </div>


                            <div>
                                <!-- Label -->
                                <x-label for="type" value="{{ __('Tipe Kontrak') }}" />

                                <!-- Dropdown Select -->
                                <select id="type" name="type" class="mt-1 block w-full form-select"
                                    wire:model.live="state.type">
                                    <option value="">Pilih Tipe Kontrak</option>
                                    @foreach ($mstType as $type)
                                        <option value="{{ $type->type }}">
                                            {{ ucwords(str_replace('_', ' ', $type->type)) }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>

                            <div>
                                <x-label for="path" value="{{ __('Path Contract') }}" />
                                <x-input id="path" type="file" name="path" class="mt-1 block w-full"
                                    wire:model.live="state.path" autocomplete="path" />
                                <x-input-error for="path" class="mt-2" />
                            </div>

                            <div>
                                <!-- Label -->
                                <x-label for="bill_type" value="{{ __('Tipe Pembayaran') }}" />

                                {{-- Input Container --}}
                                <div id="input-container" class="space-y-4">
                                    <div class="input-group flex items-center gap-2">
                                        <x-input type="text" name="bill_type[]" class="mt-1 block w-full"
                                            placeholder="Masukkan teks" />
                                        <button type="button"
                                            class="add-input btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">
                                            <!-- Plus Icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                        <button type="button"
                                            class="remove-input btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300"
                                            disabled>
                                            <!-- Minus Icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 12H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <x-label for="address" value="{{ __('Alamat') }}" />
                                <x-input-wide id="address" type="text" name="address"
                                    class="mt-1 block w-full min-h-[40px]" placeholder="Masukkan alamat"
                                    wire:model.live="state.address" required autocomplete="address" />
                                <x-input-error for="address" class="mt-2" />
                            </div>

                            <div>
                                <!-- Label -->
                                <x-label for="work_unit" value="{{ __('Unit Kerja') }}" />

                                <!-- Dropdown Select -->
                                <select id="work_unit" name="work_unit" class="mt-1 block w-full form-select "
                                    wire:model.live="state.bill_type">
                                    <option value="">Unit Kerja</option>
                                    @foreach ($mstWorkUnit as $workUnit)
                                        <option value="{{ $workUnit->work_unit }}">
                                            {{ ucwords(str_replace('_', ' ', $workUnit->work_unit)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-gray-700/20 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                            <x-secondary-button
                                onclick="window.location='{{ route('contracts.index') }}'">Batal</x-secondary-button>
                            <div class="form-group">
                                <x-button type="submit">Simpan</x-button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            {{-- Tambah Data Baru End --}}
        </div>
    </div>
    {{-- JavaScript untuk format Rupiah --}}
    <script>
        // # Format Rupiah
        function formatRupiah(input) {
            let value = input.value.replace(/\D/g, ""); // Hanya angka
            let formatted = new Intl.NumberFormat("id-ID").format(value); // Format ke Rp 50.000
            input.value = "Rp " + formatted;

            // Set nilai ke input hidden (tanpa format)
            document.getElementById("value_hidden").value = value;
        }

        function removeFormat(input) {
            if (input.value === "" || input.value === "Rp ") {
                input.value = "Rp 0"; // Jika kosong, tetap tampilkan Rp
            }
        }


        // # Format Tanggal Dibatasin
        function setMinEndDate() {
            let startDate = document.getElementById("start_date").value;
            document.getElementById("end_date").setAttribute("min", startDate);
        }

        // # Input Tipe Pembayaran

        // Fungsi untuk menambahkan form baru
        function addNewInput() {
            const container = document.getElementById('input-container');
            const newInput = document.createElement('div');
            newInput.classList.add('input-group', 'flex', 'items-center', 'gap-2');
            newInput.innerHTML = `
        {{-- Input Field Baru --}}
        <x-input type="text" name="bill_type[]" class="mt-1 block w-full" placeholder="Masukkan teks" />

        {{-- Tombol Plus (+) --}}
        <button type="button" class="add-input btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">
            <!-- Plus Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </button>

        {{-- Tombol Minus (-) --}}
        <button type="button" class="remove-input btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">
            <!-- Minus Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
            </svg>
        </button>
    `;
            container.appendChild(newInput);

            // Tambahkan event listener untuk tombol plus di form baru
            newInput.querySelector('.add-input').addEventListener('click', addNewInput);
        }

        // Tambahkan event listener untuk tombol plus di form pertama
        document.querySelector('.add-input').addEventListener('click', addNewInput);

        // Hapus Input
        document.getElementById('input-container').addEventListener('click', function(e) {
            if (e.target.closest('.remove-input')) {
                const inputGroups = document.querySelectorAll('.input-group');
                if (inputGroups.length > 1) { // Pastikan minimal ada 1 input field
                    e.target.closest('.input-group').remove();
                }
            }
        });
    </script>


</x-app-layout>
