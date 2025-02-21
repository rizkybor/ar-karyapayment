<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <form action="{{ route('management-fee.create') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Dashboard actions -->
            <div class="sm:flex sm:justify-between sm:items-center mb-8">
                <!-- Left: Title -->
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Tambah Data Baru</h1>
                </div>

                <!-- Right: Actions -->
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                    <x-secondary-button onclick="window.location='{{ route('management-fee.index') }}'"
                        class="float-right">
                        Batal
                    </x-secondary-button>

                    <x-secondary-button onclick="window.location='submit'" class="float-right">
                        Simpan
                    </x-secondary-button>
                </div>
            </div>
            <!-- Dashboard actions end -->

            {{-- Tambah Data Baru --}}
            <div class="mt-5 md:mt-0 md:col-span-2">
                <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <!-- Label -->
                            <x-label for="type" value="{{ __('Kontrak') }}" />

                            <!-- Dropdown Select -->
                            <select id="contract_id" name="contract_id" class="mt-1 block w-full form-select"
                                onchange="updateContractDetails()">
                                <option value="">Pilih Kontrak</option>
                                @foreach ($contracts as $contract)
                                    <option value="{{ $contract->id }}" data-employee="{{ $contract->employee_name }}"
                                        data-type="{{ $contract->type }}">
                                        {{ $contract->contract_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-label for="letter_number" value="{{ __('No Surat') }}" />
                            <x-input id="letter_number" name="letter_number" placeholder="Auto" type="text"
                                class="mt-1 block w-full min-h-[40px]" wire:model.live="state.letter_number" required
                                autocomplete="letter_number" disabled value="{{ $letterNumber }}" />
                            <x-input-error for="letter_number" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="invoice_number" value="{{ __('No Invoice') }}" />
                            <x-input id="invoice_number" name="invoice_number" placeholder="Auto" type="text"
                                class="mt-1 block w-full min-h-[40px]" wire:model.live="state.invoice_number" required
                                autocomplete="invoice_number" disabled value="{{ $invoiceNumber }}" />
                            <x-input-error for="invoice_number" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="period" value="{{ __('Periode / Termin') }}" />
                            <x-input id="period" name="period" placeholder="Masukkan Periode / Termin"
                                type="text" class="mt-1 block w-full min-h-[40px]" wire:model.live="state.period"
                                required autocomplete="period" />
                            <x-input-error for="period" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="receipt_number" value="{{ __('No Kwitansi') }}" />
                            <x-input id="receipt_number" name="receipt_number" placeholder="Auto" type="text"
                                class="mt-1 block w-full min-h-[40px]" wire:model.live="state.receipt_number" required
                                autocomplete="receipt_number" disabled value="{{ $receiptNumber }}" />
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
                                class="mt-1 block w-full min-h-[40px]" wire:model.live="state.employee_name" required
                                autocomplete="employee_name" disabled />
                            <x-input-error for="employee_name" class="mt-2" />
                        </div>
                        <div class="sm:row-start-5">
                            <x-label for="type" value="{{ __('Type') }}" />
                            <x-input id="type" name="type" placeholder="Auto" type="text"
                                class="mt-1 block w-full min-h-[40px]" wire:model.live="state.type" required
                                autocomplete="type" disabled />
                            <x-input-error for="type" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>
            {{-- Tambah Data Baru End --}}
        </form>
    </div>
    <script>
        function updateContractDetails() {
            let contractSelect = document.getElementById("contract_id");
            let selectedOption = contractSelect.options[contractSelect.selectedIndex];

            let employeeName = selectedOption.getAttribute("data-employee") || "";
            let contractType = selectedOption.getAttribute("data-type") || "";

            // Ubah format type dari snake_case ke Title Case
            let formattedType = contractType.replace(/_/g, " ").replace(/\b\w/g, char => char.toUpperCase());

            document.getElementById("employee_name").value = employeeName;
            document.getElementById("type").value = formattedType;
        }
    </script>
</x-app-layout>
