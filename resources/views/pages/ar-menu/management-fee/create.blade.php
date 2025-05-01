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
                    <x-button-action color="violet" type="submit">Simpan</x-button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tambahkan input hidden untuk base number -->
    <input type="hidden" id="base_number" value="{{ $baseNumber }}">

    <script>
        function getCompanyInitial(employeeName) {
            if (!employeeName) return 'SOL';

            // Hapus PT. dari nama perusahaan
            const companyName = employeeName.replace(/^PT\.\s*/i, '');

            // Split nama menjadi kata-kata
            const words = companyName.trim().split(/\s+/);

            // Jika hanya 1 kata
            if (words.length === 1) {
                return words[0];
            }

            // Jika lebih dari 1 kata
            let initials = '';
            for (let i = 0; i < words.length; i++) {
                if (words[i].length > 0) {
                    initials += words[i][0].toUpperCase();
                }
            }

            return initials;
        }

        function updateContractDetails(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const employeeName = selectedOption.getAttribute("data-employee") || "";

            // Update nama pemberi kerja
            document.getElementById("employee_name").value = employeeName;

            // Update tipe tagihan
            const billTypes = JSON.parse(selectedOption.getAttribute("data-bill-types")) || [];
            const billTypeSelect = document.getElementById("bill_type");
            billTypeSelect.innerHTML = '<option value="">Pilih Type Tagihan</option>';
            billTypes.forEach(billType => {
                const option = document.createElement("option");
                option.value = billType;
                option.textContent = billType;
                billTypeSelect.appendChild(option);
            });

            // Generate nomor dokumen
            const baseNumber = '{{ $baseNumber }}';
            const monthRoman = '{{ $monthRoman }}';
            const year = '{{ $year }}';
            const companyInitial = getCompanyInitial(employeeName);

            document.getElementById('letter_number').value =
                `${baseNumber}/MF/KEU/KPU/${companyInitial}/${monthRoman}/${year}`;
            document.getElementById('invoice_number').value =
                `${baseNumber}/MF/KW/KPU/${companyInitial}/${monthRoman}/${year}`;
            document.getElementById('receipt_number').value =
                `${baseNumber}/MF/INV/KPU/${companyInitial}/${monthRoman}/${year}`;
        }

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const contractSelect = document.getElementById('contract_id');
            contractSelect.addEventListener('change', function() {
                updateContractDetails(this);
            });

            // Jika ada kontrak yang dipilih (setelah validasi gagal)
            @if (old('contract_id'))
                contractSelect.value = '{{ old('contract_id') }}';
                updateContractDetails(contractSelect);
            @endif
        });
    </script>
</x-app-layout>
