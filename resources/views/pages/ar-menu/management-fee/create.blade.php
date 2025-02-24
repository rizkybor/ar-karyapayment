<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Tambah Data Baru</h1>
            </div>
        </div>
        <form action="{{ route('management-fee.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
                <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-label for="type" value="{{ __('Kontrak') }}" />
                            <select id="contract_id" name="contract_id" class="mt-1 block w-full form-select"
                                onchange="updateContractDetails(this)">
                                <option value="">Pilih Kontrak</option>
                                @foreach ($contracts as $contract)
                                    <option value="{{ $contract->id }}" data-employee="{{ $contract->employee_name }}"
                                        data-bill-types="{{ $contract->billTypes->pluck('bill_type')->toJson() }}">
                                        {{ $contract->contract_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-label for="letter_number" value="{{ __('No Surat') }}" />
                            <x-input id="letter_number" name="letter_number" placeholder="Auto" type="text"
                                class="mt-1 block w-full min-h-[40px]" readonly value="{{ $letterNumber }}" />
                            <x-input-error for="letter_number" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="invoice_number" value="{{ __('No Invoice') }}" />
                            <x-input id="invoice_number" name="invoice_number" placeholder="Auto" type="text"
                                class="mt-1 block w-full min-h-[40px]" readonly value="{{ $invoiceNumber }}" />
                            <x-input-error for="invoice_number" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="period" value="{{ __('Periode / Termin') }}" />
                            <x-input id="period" name="period" placeholder="Masukkan Periode / Termin"
                                type="text" class="mt-1 block w-full min-h-[40px]" required />
                            <x-input-error for="period" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="receipt_number" value="{{ __('No Kwitansi') }}" />
                            <x-input id="receipt_number" name="receipt_number" placeholder="Auto" type="text"
                                class="mt-1 block w-full min-h-[40px]" readonly value="{{ $receiptNumber }}" />
                            <x-input-error for="receipt_number" class="mt-2" />
                        </div>
                        <div class="sm:row-span-2">
                            <x-label for="letter_subject" value="{{ __('Perihal Surat') }}" />
                            <x-input-wide id="letter_subject" name="letter_subject" placeholder="Masukkan Perihal Surat"
                                type="text" class="mt-1 block w-full min-h-[40px]" />
                            <x-input-error for="letter_subject" class="mt-2" />
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
                                <!-- Opsi akan diisi oleh JS -->
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
                    <x-button type="submit">Simpan</x-button>
                </div>
            </div>
        </form>
    </div>
    <script>
        function updateContractDetails(selectElement) {
            let selectedOption = selectElement.options[selectElement.selectedIndex];

            // Update Nama Pemberi Kerja
            let employeeName = selectedOption.getAttribute("data-employee") || "";
            document.getElementById("employee_name").value = employeeName;

            // Update Type Tagihan
            let billTypes = JSON.parse(selectedOption.getAttribute("data-bill-types")) || [];
            let billTypeSelect = document.getElementById("bill_type");
            billTypeSelect.innerHTML = '<option value="">Pilih Type Tagihan</option>';

            billTypes.forEach(billType => {
                let option = document.createElement("option");
                option.value = billType; // Mengisi value dengan bill_type
                option.textContent = billType;
                billTypeSelect.appendChild(option);
            });
        }
    </script>
</x-app-layout>
