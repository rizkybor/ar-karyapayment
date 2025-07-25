<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Buat Invoice Management Fee
                </h1>
            </div>
        </div>
        <form action="{{ route('management-fee.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
                <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                        {{-- Contract --}}
                        <div class="relative">
                            <x-label for="type" value="{{ __('Kontrak') }}" />
                            <input type="text" id="contractInput" placeholder="Ketik/Pilih Kontrak..."
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md 
                                bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-200 
                                focus:outline-none focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all"
                                oninput="filterContractDropdown()" onclick="toggleContractDropdown()"
                                autocomplete="off" />

                            <input type="hidden" id="contract_id" name="contract_id"
                                value="{{ old('contract_id') }}" />
                            <input type="hidden" id="contract_initial" name="contract_initial" value="" />

                            <ul id="contractDropdown"
                                class="absolute z-10 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 
                                rounded-md mt-1 max-h-60 overflow-auto shadow-md hidden transition-all">
                                @foreach ($contracts as $contract)
                                    <li class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                                        onclick="selectContract(
                                            '{{ $contract->id }}', 
                                            '{{ $contract->contract_number }}', 
                                            '{{ $contract->employee_name }}', 
                                            '{{ $contract->billTypes->pluck('bill_type')->toJson() }}',
                                            '{{ $contract->contract_initial ?? 'SOL' }}'
                                        )">
                                        <div class="font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $contract->contract_number }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $contract->employee_name }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Document --}}
                        <div>
                            <x-label for="letter_number" value="{{ __('No Surat') }}" />
                            <x-input id="letter_number" name="letter_number" placeholder="Auto" type="text"
                                class="mt-1 block w-full min-h-[40px]" readonly />
                            <x-input-error for="letter_number" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="invoice_number" value="{{ __('No Invoice') }}" />
                            <x-input id="invoice_number" name="invoice_number" placeholder="Auto" type="text"
                                class="mt-1 block w-full min-h-[40px]" readonly />
                            <x-input-error for="invoice_number" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="period" value="{{ __('Periode / Termin') }}" />
                            <x-input id="period" name="period"
                                placeholder="Masukkan Periode / Termin, contoh : (periode Mei)" type="text"
                                class="mt-1 block w-full min-h-[40px]" />
                            <x-input-error for="period" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="receipt_number" value="{{ __('No Kwitansi') }}" />
                            <x-input id="receipt_number" name="receipt_number" placeholder="Auto" type="text"
                                class="mt-1 block w-full min-h-[40px]" readonly />
                            <x-input-error for="receipt_number" class="mt-2" />
                        </div>
                        <div class="sm:row-span-2">
                            <x-label for="letter_subject" value="{{ __('Perihal Surat') }}" />
                            <x-input-wide id="letter_subject" name="letter_subject" placeholder="Masukkan Perihal Surat"
                                type="text" class="mt-1 block w-full min-h-[40px]" />
                            <x-input-error for="letter_subject" class="mt-2" />

                            <x-label class="mt-2" for="reference_document" value="{{ __('Referensi Dokumen') }}" />
                            <x-input id="reference_document" name="reference_document" placeholder="Optional"
                                type="text" class="mt-1 block w-full min-h-[40px]" />
                            <x-input-error for="reference_document" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="employee_name" value="{{ __('Nama Pemberi Kerja') }}" />
                            <x-input id="employee_name" name="employee_name" placeholder="Auto" type="text"
                                class="mt-1 block w-full min-h-[40px]" readonly />
                            <x-input-error for="employee_name" class="mt-2" />
                        </div>
                        <div class="sm:row-start-5">
                            <x-label for="bill_type" value="{{ __('Type Tagihan') }}" />
                            <select id="bill_type" name="manfee_bill" class="mt-1 block w-full form-select" required>
                                <option value="">Pilih Type Tagihan</option>
                            </select>
                            <x-input-error for="type" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                    <x-secondary-button onclick="window.location='{{ route('management-fee.index') }}'">
                        Batal
                    </x-secondary-button>
                    <x-button-action color="violet" type="submit">Simpan</x-button>
                </div>
            </div>
        </form>
    </div>


    <script>
        // Toggle contract dropdown
        function toggleContractDropdown() {
            document.getElementById("contractDropdown").classList.toggle("hidden");
        }

        // Filter contract dropdown
        function filterContractDropdown() {
            let input = document.getElementById("contractInput").value.toLowerCase();
            let items = document.querySelectorAll("#contractDropdown li");

            items.forEach(item => {
                const text = item.innerText.toLowerCase();
                item.style.display = text.includes(input) ? "" : "none";
            });
        }

        // Function saat pilih kontrak
        function selectContract(id, contractNumber, employeeName, billTypesJson, contractInitial) {
            // alert
            if (!contractInitial || contractInitial.trim() === '') {
                alert("❗ Silakan lengkapi 'Initial Kontrak' terlebih dahulu di data kontrak.");
                return;
            }
            // Update
            document.getElementById("contractInput").value = contractNumber;
            document.getElementById("contract_id").value = id;
            document.getElementById("employee_name").value = employeeName;
            document.getElementById("contract_initial").value = contractInitial;

            // Update bill types dropdown
            try {
                const billTypes = JSON.parse(billTypesJson);
                const billTypeSelect = document.getElementById("bill_type");
                billTypeSelect.innerHTML = '<option value="">Pilih Type Tagihan</option>';
                billTypes.forEach(billType => {
                    const option = document.createElement("option");
                    option.value = billType;
                    option.textContent = billType;
                    billTypeSelect.appendChild(option);
                });
            } catch (e) {
                console.warn("Failed to parse billTypes", e);
            }

            updateDocumentNumbers(contractInitial);
            document.getElementById("contractDropdown").classList.add("hidden");
        }

        document.addEventListener('click', function(event) {
            const input = document.getElementById('contractInput');
            const dropdown = document.getElementById('contractDropdown');
            if (!input.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add("hidden");
            }
        });
    </script>
</x-app-layout>
