<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            {{-- Judul --}}
            <div class="md:col-span-1 flex justify-between">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Kontrak</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Edit informasi kontrak, termasuk detail pihak terkait, durasi, dan persyaratan khusus.
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
                                    placeholder="Masukkan nomer kontrak" required maxlength="255" />
                                <x-input-error for="contract_number" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="contract_initial" value="Inisial Kontrak" />
                                <x-input id="contract_initial"
                                    value="{{ old('contract_initial', $contract->contract_initial) }}" type="text"
                                    name="contract_initial" class="mt-1 block w-full min-h-[40px]"
                                    placeholder="Masukkan inisial kontrak" required maxlength="255" />
                                <x-input-error for="contract_initial" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="title" value="{{ __('Judul Kontrak') }}" />
                                <x-input id="title" type="text" name="title"
                                    value="{{ old('title', $contract->title) }}" class="mt-1 block w-full min-h-[40px]"
                                    placeholder="Masukkan nomer kontrak" wire:model.live="state.title" required
                                    autocomplete="title" />
                                <x-input-error for="title" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="category" value="{{ __('Kategori Kontrak') }}" />
                                <select id="category" name="category"
                                    class="form-select mt-1 block w-full min-h-[40px]">
                                    @foreach ($category as $item)
                                        <option value="{{ $item }}"
                                            {{ old('category', $contract->category) == $item ? 'selected' : '' }}>
                                            {{ $item }}
                                        </option>
                                    @endforeach
                                </select>
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
                                <x-input id="value" type="text" name="formatted_value"
                                    class="mt-1 block w-full min-h-[40px]" placeholder="Masukkan Nilai Kontrak"
                                    value="Rp {{ number_format(old('value', $contract->value) ?? 0, 0, ',', '.') }}"
                                    oninput="formatRupiah(this)" onblur="removeFormat(this)" required />
                                <x-input-error for="value" class="mt-2" />
                                <!-- Input hidden untuk menyimpan angka tanpa format -->
                                <input type="hidden" id="value_hidden" name="value"
                                    value="{{ old('value', $contract->value) ?? 0 }}">
                            </div>

                            <div>
                                <div>
                                    <x-label for="contract_date" value="Tanggal Kontrak" />
                                    <x-input id="contract_date"
                                        value="{{ old('contract_date', $contract->contract_date) }}" type="date"
                                        name="contract_date" class="mt-1 block w-full min-h-[40px]"
                                        oninput="setMinStartDate()" />
                                    <x-input-error for="contract_date" class="mt-2" />
                                </div>
                            </div>

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

                            {{-- option active --}}
                            {{-- <div>
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
                            </div> --}}

                            <div>
                                <x-label for="type" value="Tipe Kontrak" />
                                <select id="type" name="type" class="mt-1 block w-full form-select" disabled>
                                    @foreach ($mstType as $type)
                                        <option value="{{ $type->type }}"
                                            {{ $contract->type == $type->type ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $type->type)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="type" value="{{ $contract->type }}">
                            </div>


                            <div>
                                <x-label for="path" value="Path Contract" />
                                <x-input id="path" type="file" name="path" class="mt-1 block w-full" />
                                <p class="text-sm text-gray-500">{{ $contract->path }}</p>
                                <x-input-error for="path" class="mt-2" />
                            </div>

                            {{-- Form Tipe Pembayaran --}}
                            <div id="bill-type-container"
                                class="{{ $contract->type === 'management_fee' ? '' : 'hidden' }}">
                                <x-label for="bill_type" value="Tipe Pembayaran" />
                                <div id="input-container" class="space-y-4">
                                    {{-- Tampilkan input bill_type yang sudah ada --}}
                                    @foreach ($mstBillType as $index => $billType)
                                        <div class="input-group flex items-center gap-2">
                                            <x-input type="text" name="bill_type[]" class="block w-full"
                                                value="{{ $billType->bill_type }}" placeholder="Masukkan teks" />
                                            <button type="button"
                                                class="add-input btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">
                                                <!-- Plus Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                            <button type="button"
                                                class="remove-input btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">
                                                <!-- Minus Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach

                                    {{-- Tambahkan input kosong jika tidak ada data bill_type --}}
                                    @if ($mstBillType->isEmpty())
                                        <div class="input-group flex items-center gap-2">
                                            <x-input type="text" name="bill_type[]" class="block w-full"
                                                placeholder="Masukkan teks" />
                                            <button type="button"
                                                class="add-input btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">
                                                <!-- Plus Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                            <button type="button"
                                                class="remove-input btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">
                                                <!-- Minus Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <x-label for="address" value="Alamat" />
                                <x-input id="address" value="{{ old('address', $contract->address) }}"
                                    type="text" name="address" class="mt-1 block w-full min-h-[40px]"
                                    placeholder="Masukkan alamat" required />
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


                            <hr />

                            <x-label>*Informasi Tambahan untuk keterangan Departemen, Proyek, Segmen Usaha
                            </x-label>

                            {{-- Autocomplete Department --}}
                            <div class="relative">
                                <x-label for="departmentInput" value="Departemen" />
                                <input type="text" id="departmentInput" placeholder="Ketik/Pilih Departemen..."
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md 
                                    bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-200 
                                    focus:outline-none focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all"
                                    oninput="filterDepartmentDropdown()" onclick="toggleDepartmentDropdown()"
                                    autocomplete="off" />

                                <input type="hidden" id="department_id" name="departmentId"
                                    value="{{ old('departmentId', $contract->departmentId) }}" />

                                <ul id="departmentDropdown"
                                    class="absolute z-10 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 
                                    rounded-md mt-1 max-h-60 overflow-auto shadow-md hidden transition-all">

                                    {{-- Opsi kosong --}}
                                    <li class="px-4 py-2 cursor-pointer text-gray-500 italic hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                                        onclick="selectDepartment('', '')">
                                        Kosongkan departemen
                                    </li>

                                    @foreach ($departmentList as $dept)
                                        <li class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                                            onclick="selectDepartment('{{ $dept['id'] }}', '{{ $dept['name'] }}')">
                                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                {{ $dept['name'] }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Autocomplete Proyek --}}
                            <div class="relative mt-1">
                                <x-label for="projectInput" value="Proyek" />
                                <input type="text" id="projectInput" placeholder="Ketik/Pilih Proyek..."
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md 
                                    bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-200 
                                    focus:outline-none focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all"
                                    oninput="filterProjectDropdown()" onclick="toggleProjectDropdown()"
                                    autocomplete="off" />

                                <input type="hidden" id="project_id" name="projectId"
                                    value="{{ old('projectId', $contract->projectId) }}" />

                                <ul id="projectDropdown"
                                    class="absolute z-10 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 
                                    rounded-md mt-1 max-h-60 overflow-auto shadow-md hidden transition-all">

                                    {{-- Opsi kosong --}}
                                    <li class="px-4 py-2 cursor-pointer text-gray-500 italic hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                                        onclick="selectProject('', '')">
                                        Kosongkan Project
                                    </li>

                                    @foreach ($dataProjectList as $proj)
                                        <li class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                                            onclick="selectProject('{{ $proj['id'] }}', '{{ $proj['name'] }}')">
                                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                {{ $proj['name'] }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Autocomplete Segmen Usaha --}}
                            <div class="relative mt-1">
                                <x-label for="classificationInput" value="Kategori Keuangan / Segmen Usaha" />
                                <input type="text" id="classificationInput" placeholder="Ketik/Pilih Kategori..."
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md 
                                    bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-200 
                                    focus:outline-none focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all"
                                    oninput="filterClassificationDropdown()" onclick="toggleClassificationDropdown()"
                                    autocomplete="off" />

                                <input type="hidden" id="classification_id" name="segmenUsahaId"
                                    value="{{ old('segmenUsahaId', $contract->segmenUsahaId) }}" />

                                <ul id="classificationDropdown"
                                    class="absolute z-10 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 
                                    rounded-md mt-1 max-h-60 overflow-auto shadow-md hidden transition-all">

                                    {{-- Opsi kosong --}}
                                    <li class="px-4 py-2 cursor-pointer text-gray-500 italic hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                                        onclick="selectClassification('', '')">
                                        Kosongkan segmen usaha
                                    </li>

                                    @foreach ($dataClassificationList as $item)
                                        <li class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                                            onclick="selectClassification('{{ $item['id'] }}', '{{ $item['name'] }}')">
                                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                {{ $item['name'] }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>


                            {{-- <input type="hidden" id="department_id" name="departmentId"
                                value="{{ old('departmentId', $contract->departmentId) }}">
                            <input type="hidden" id="project_id" name="projectId"
                                value="{{ old('projectId', $contract->projectId) }}">
                            <input type="hidden" id="classification_id" name="segmenUsahaId"
                                value="{{ old('segmenUsahaId', $contract->segmenUsahaId) }}"> --}}

                            <hr />

                        </div>
                        <div
                            class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-gray-700/20 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md gap-2">
                            <x-secondary-button
                                onclick="window.location='{{ route('contracts.index') }}'">Batal</x-secondary-button>
                            <x-button type="submit">Update Kontrak</x-button>
                        </div>
                    </div>

                </form>
            </div>
            {{-- Edit Data Baru End --}}
        </div>
    </div>



    <script>
        function filterDepartmentDropdown() {
            const input = document.getElementById("departmentInput");
            const filter = input.value.toLowerCase();
            const dropdown = document.getElementById("departmentDropdown");
            const items = dropdown.getElementsByTagName("li");

            dropdown.classList.remove("hidden");

            for (let i = 0; i < items.length; i++) {
                const text = items[i].innerText.toLowerCase();
                items[i].style.display = text.includes(filter) ? "" : "none";
            }
        }

        function toggleDepartmentDropdown() {
            const dropdown = document.getElementById("departmentDropdown");
            dropdown.classList.toggle("hidden");
        }

        function selectDepartment(id, name) {
            document.getElementById("department_id").value = id;
            document.getElementById("departmentInput").value = name;
            document.getElementById("departmentDropdown").classList.add("hidden");
        }
    </script>

    <script>
        function filterProjectDropdown() {
            const input = document.getElementById("projectInput");
            const filter = input.value.toLowerCase();
            const dropdown = document.getElementById("projectDropdown");
            const items = dropdown.getElementsByTagName("li");

            dropdown.classList.remove("hidden");

            for (let i = 0; i < items.length; i++) {
                const text = items[i].innerText.toLowerCase();
                items[i].style.display = text.includes(filter) ? "" : "none";
            }
        }

        function toggleProjectDropdown() {
            const dropdown = document.getElementById("projectDropdown");
            dropdown.classList.toggle("hidden");
        }

        function selectProject(id, name) {
            document.getElementById("project_id").value = id;
            document.getElementById("projectInput").value = name;
            document.getElementById("projectDropdown").classList.add("hidden");
        }
    </script>

    <script>
        function filterClassificationDropdown() {
            const input = document.getElementById("classificationInput");
            const filter = input.value.toLowerCase();
            const dropdown = document.getElementById("classificationDropdown");
            const items = dropdown.getElementsByTagName("li");

            dropdown.classList.remove("hidden");

            for (let i = 0; i < items.length; i++) {
                const text = items[i].innerText.toLowerCase();
                items[i].style.display = text.includes(filter) ? "" : "none";
            }
        }

        function toggleClassificationDropdown() {
            const dropdown = document.getElementById("classificationDropdown");
            dropdown.classList.toggle("hidden");
        }

        function selectClassification(id, name) {
            document.getElementById("classification_id").value = id;
            document.getElementById("classificationInput").value = name;
            document.getElementById("classificationDropdown").classList.add("hidden");
        }
    </script>

    {{-- JavaScript --}}
    <script>
        // bill type
        document.addEventListener('DOMContentLoaded', function() {
            const typeDropdown = document.getElementById('type');
            const billTypeContainer = document.getElementById('bill-type-container');
            const inputContainer = document.getElementById('input-container');

            // === Departemen ===
            const departmentId = document.getElementById("department_id").value;
            const departmentInput = document.getElementById("departmentInput");
            if (departmentId) {
                const selectedDept = [...document.querySelectorAll("#departmentDropdown li")].find(li =>
                    li.getAttribute("onclick")?.includes(`'${departmentId}'`)
                );
                if (selectedDept) {
                    departmentInput.value = selectedDept.innerText.trim();
                }
            }

            // === Proyek ===
            const projectId = document.getElementById("project_id").value;
            const projectInput = document.getElementById("projectInput");
            if (projectId) {
                const selectedProj = [...document.querySelectorAll("#projectDropdown li")].find(li =>
                    li.getAttribute("onclick")?.includes(`'${projectId}'`)
                );
                if (selectedProj) {
                    projectInput.value = selectedProj.innerText.trim();
                }
            }

            // === Kategori Keuangan / Segmen Usaha ===
            const classificationId = document.getElementById("classification_id").value;
            const classificationInput = document.getElementById("classificationInput");
            if (classificationId) {
                const selectedClass = [...document.querySelectorAll("#classificationDropdown li")].find(li =>
                    li.getAttribute("onclick")?.includes(`'${classificationId}'`)
                );
                if (selectedClass) {
                    classificationInput.value = selectedClass.innerText.trim();
                }
            }

            // Fungsi untuk menampilkan/menyembunyikan form Tipe Pembayaran
            function toggleBillTypeForm() {
                if (typeDropdown.value === 'management_fee') {
                    billTypeContainer.classList.remove('hidden');

                    // Pastikan ada minimal satu input field
                    if (inputContainer.querySelectorAll('.input-group').length === 0) {
                        addNewInput();
                    }
                } else {
                    billTypeContainer.classList.add('hidden');
                }
            }

            // Panggil fungsi saat halaman dimuat
            toggleBillTypeForm();

            // Panggil fungsi saat dropdown berubah
            typeDropdown.addEventListener('change', toggleBillTypeForm);

            // Fungsi untuk menambahkan input baru
            function addNewInput() {
                const newInput = document.createElement('div');
                newInput.classList.add('input-group', 'flex', 'items-center', 'gap-2');

                newInput.innerHTML = `
      
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

                inputContainer.appendChild(newInput);
            }


            // Event listener untuk tombol "Tambah Input"
            document.addEventListener('click', function(e) {
                if (e.target.closest('.add-input')) {
                    addNewInput();
                }
            });

            // Event listener untuk tombol "Hapus Input"
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-input')) {
                    const inputGroups = inputContainer.querySelectorAll('.input-group');
                    if (inputGroups.length > 1) {
                        e.target.closest('.input-group').remove();
                    }
                }
            });
        });

        // Fungsi untuk format Rupiah
        function formatRupiah(input) {
            let value = input.value.replace(/\D/g, ""); // Hanya angka
            let formatted = new Intl.NumberFormat("id-ID").format(value); // Format ke Rp
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
        function setMinStartDate() {
            let startDate = document.getElementById("contract_date").value;
            document.getElementById("start_date").setAttribute("min", startDate);
        }

        // Format Tanggal
        function setMinEndDate() {
            let startDate = document.getElementById("start_date").value;
            document.getElementById("end_date").setAttribute("min", startDate);
        }
    </script>
</x-app-layout>
