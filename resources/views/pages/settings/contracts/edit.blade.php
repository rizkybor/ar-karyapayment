<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            {{-- Judul --}}
            <div class="md:col-span-1 flex justify-between">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Kontrak</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Edit informasi kontrak baru, termasuk detail pihak terkait, durasi, dan persyaratan khusus.
                    </p>
                </div>
            </div>
            {{-- Judul End --}}

            {{-- Edit Data Baru --}}
            <div class="mt-5 md:mt-0 md:col-span-2">

                <form action="{{ route('contracts.update', $contract->id) }}" method="POST" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="grid grid-cols-1 gap-y-6">
                            <div>
                                <x-label for="contract_number" value="Nomor Kontrak" />
                                <x-input id="contract_number"
                                    value="{{ old('contract_number', $contract->contract_number) }}" type="text"
                                    name="contract_number" class="mt-1 block w-full min-h-[40px]"
                                    placeholder="Masukkan nomor kontrak" required maxlength="10" />
                                <x-input-error for="contract_number" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="employee_name" value="Nama Perusahaan" />
                                <x-input id="employee_name" value="{{ old('employee_name', $contract->employee_name) }}"
                                    type="text" name="employee_name" class="mt-1 block w-full min-h-[40px]"
                                    placeholder="Masukkan nama perusahaan" required />
                                <x-input-error for="employee_name" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="value" value="Nilai Kontrak" />
                                <x-input id="value" value="{{ old('value', $contract->value) }}" type="text"
                                    name="formatted_value" class="mt-1 block w-full min-h-[40px]"
                                    placeholder="Masukkan Nilai Kontrak" oninput="formatRupiah(this)"
                                    onblur="removeFormat(this)" required />
                                <x-input-error for="value" class="mt-2" />
                            </div>

                            <!-- Input Hidden untuk Database -->
                            <input type="hidden" name="value" id="value_hidden" value="{{ $contract->value }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-label for="start_date" value="Tanggal Mulai" />
                                    <x-input id="start_date" value="{{ old('start_date', $contract->start_date) }}"
                                        type="date" name="start_date" class="mt-1 block w-full min-h-[40px]"
                                        oninput="setMinEndDate()" required />
                                    <x-input-error for="start_date" class="mt-2" />
                                </div>

                                <div>
                                    <x-label for="end_date" value="Tanggal Selesai" />
                                    <x-input id="end_date" value="{{ old('end_date', $contract->end_date) }}"
                                        type="date" name="end_date" class="mt-1 block w-full min-h-[40px]"
                                        required />
                                    <x-input-error for="end_date" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <x-label for="type" value="Tipe Kontrak" />
                                <select id="type" name="type" class="mt-1 block w-full form-select">
                                    <option value="">Pilih Tipe Kontrak</option>
                                    @foreach ($mstType as $type)
                                        <option value="{{ $type->type }}"
                                            {{ old('type', $contract->type) == $type->type ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $type->type)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-label for="path" value="Path Contract" />
                                <x-input id="path" type="file" name="path" class="mt-1 block w-full" />
                                <p class="text-sm text-gray-500">{{ $contract->path }}</p>
                                <!-- Tampilkan path lama -->
                                <x-input-error for="path" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="bill_type" value="Tipe Pembayaran" />
                                <select id="bill_type" name="bill_type" class="mt-1 block w-full form-select">
                                    <option value="">Pilih Tipe Pembayaran</option>
                                    @foreach ($mstBillType as $billType)
                                        <option value="{{ $billType->bill_type }}"
                                            {{ old('bill_type', $contract->bill_type) == $billType->bill_type ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $billType->bill_type)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-label for="address" value="Alamat" />
                                <x-input id="address" value="{{ old('address', $contract->address) }}" type="text"
                                    name="address" class="mt-1 block w-full min-h-[40px]" placeholder="Masukkan alamat"
                                    required />
                                <x-input-error for="address" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="work_unit" value="Unit Kerja" />
                                <select id="work_unit" name="work_unit" class="mt-1 block w-full form-select">
                                    <option value="">Pilih Unit Kerja</option>
                                    @foreach ($mstWorkUnit as $workUnit)
                                        <option value="{{ $workUnit->work_unit }}"
                                            {{ old('work_unit', $contract->work_unit) == $workUnit->work_unit ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $workUnit->work_unit)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-gray-700/20 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                        <x-secondary-button
                            onclick="window.location='{{ route('contracts.index') }}'">Batal</x-secondary-button>
                        <x-button type="submit">Simpan</x-button>
                    </div>
                </form>


            </div>
            {{-- Edit Data Baru End --}}
        </div>
    </div>
    {{-- JavaScript untuk format Rupiah --}}
    <script>
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

        function setMinEndDate() {
            let startDate = document.getElementById("start_date").value;
            document.getElementById("end_date").setAttribute("min", startDate);
        }
    </script>


</x-app-layout>
