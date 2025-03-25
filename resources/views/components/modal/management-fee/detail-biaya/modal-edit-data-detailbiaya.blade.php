@props(['manfeeDoc', 'detailPaymentId', 'jenis_biaya', 'account_detailbiaya'])

<!-- Modal for Editing Cost Details -->
<div x-data="{ modalOpen: false }">
    <x-button-action class="px-4 py-2 text-white rounded-md" @click="modalOpen = true" color="yellow"
        icon="pencil">Edit</x-button-action>

    <div class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-30 z-50 flex justify-center items-start pt-20"
        x-show="modalOpen" x-cloak>
        <div class="absolute bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Edit Detail Biaya</h3>

            <form class="text-left"
                action="{{ route('management-fee.detail_payments.update', ['id' => $manfeeDoc->id, 'detail_payment_id' => $detailPaymentId->id]) }}"
                method="POST" id="editForm">
                @csrf
                @method('PUT')

                <!-- Jenis Biaya -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="expense_type">Jenis
                        Biaya</label>
                    <select id="expense_type" name="expense_type"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                        <option value="" disabled>Pilih Jenis Biaya</option>
                        @foreach ($jenis_biaya as $jenis)
                            <option value="{{ $jenis }}"
                                {{ $detailPaymentId->expense_type == $jenis ? 'selected' : '' }}>{{ $jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- @php
                    dd($account_detailbiaya);
                @endphp --}}
                <!-- Account -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        for="account">Account</label>
                    <select id="account" name="account"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        onchange="updateAccountName()" required>
                        @foreach ($account_detailbiaya as $akun)
                            <option value="{{ $akun['no'] }}" data-name="{{ $akun['name'] }}"
                                {{ old('akun', $firstAccumulatedCost->account ?? '') == $akun['no'] ? 'selected' : '' }}>
                                ({{ $akun['no'] }})
                                {{ $akun['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Hidden Account Name -->
                <input type="hidden" id="account_name" name="account_name" value="">

                <!-- Uraian -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        for="uraian">Uraian</label>
                    <input type="text" id="uraian" name="uraian" value="{{ $detailPaymentId->uraian }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                </div>

                <!-- Nilai Biaya -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="nilai_biaya">Nilai
                        Biaya</label>
                    <x-input id="nilai_biaya" class="block mt-1 w-full" type="text" name="nilai_biaya"
                        value="{{ old('nilai_biaya', number_format($detailPaymentId->nilai_biaya ?? 0, 0, ',', '.')) }}"
                        oninput="formatCurrency(this);" />
                </div>

                <div class="flex justify-end gap-2">
                    <x-button-action color="red" class="px-4 py-2 bg-gray-500 text-white rounded-md"
                        @click.prevent="modalOpen = false">Batal</x-button>
                        <x-button-action color="violet" type="submit">Simpan</x-button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inisialisasi account_name saat halaman dimuat
        updateAccountName();

        // Format Rupiah Nilai Biaya
        window.formatCurrency = function(input) {
            let value = input.value.replace(/\D/g, ''); // Hanya angka
            if (value === '') return;
            input.value = new Intl.NumberFormat("id-ID").format(value);
            checkChanges();
        };
    });

    // account_name
    function updateAccountName() {
        let accountSelect = document.getElementById("account");
        let selectedOption = accountSelect.options[accountSelect.selectedIndex];
        document.getElementById("account_name").value = selectedOption.getAttribute("data-name");
    }
</script>
