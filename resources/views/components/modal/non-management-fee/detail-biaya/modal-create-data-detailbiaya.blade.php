@props(['nonManfeeDocument', 'jenis_biaya', 'account_detailbiaya'])

<!-- Modal for Adding Cost Details -->
<div x-data="{ modalOpen: false }" x-init="$watch('$root.selectedExpenseType', value => selectedExpenseType = value)">
    <x-button-action class="px-4 py-2 text-white rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
        color="violet" @click="modalOpen = true" x-bind:disabled="!selectedExpenseType">
        <span x-text="selectedExpenseType ? `+ Detail ${selectedExpenseType}` : 'Pilih Jenis Biaya'"></span>
    </x-button-action>


    <div class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 flex items-center justify-center" x-show="modalOpen" x-cloak>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full"
            @click.outside="modalOpen = false">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Tambah Detail <span x-text="selectedExpenseType ? selectedExpenseType : 'Pilih Jenis Biaya'"></span>
            </h3>
            <form action="{{ route('non-management-fee.detail_payments.store', ['id' => $nonManfeeDocument->id]) }}"
                method="POST">
                @csrf
                <div class="mb-4">

                    <input type="hidden" name="expense_type" x-model="selectedExpenseType">

                </div>
                <div class="mb-4">
                    <label for="account"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account</label>
                    <select id="account" name="account"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring focus:ring-blue-500"
                        onchange="updateAccountName()">
                        <option value="" disabled selected>Pilih Akun</option>
                        @foreach ($account_detailbiaya as $akun)
                            <option value="{{ $akun['no'] }}" data-name="{{ $akun['name'] }}" data-id="{{ $akun['id'] }}"
                                {{ old('akun', $firstAccumulatedCost->account ?? '') == $akun['no'] ? 'selected' : '' }}>
                                ({{ $akun['no'] }})
                                {{ $akun['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" id="accountId" name="accountId" value="">
                <input type="hidden" id="account_name" name="account_name" value="">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        for="uraian">Uraian</label>
                    <input type="text" id="account_show"
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-200 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required readonly>
                </div>

                <!-- Nilai Biaya -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="nilai_biaya">
                        Nilai Biaya
                    </label>
                    <x-input id="nilai_biaya" class="block mt-1 w-full" type="text" name="nilai_biaya"
                        oninput="formatCurrency(this);" inputmode="numeric" placeholder="Masukkan nilai biaya" />
                </div>
                <div class="flex justify-end gap-2">
                    <x-button-action color="red" class="px-4 py-2 bg-gray-500 text-white rounded-md"
                        @click="modalOpen = false">Batal</x-button>
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
            let value = input.value.replace(/[^\d]/g, ''); // Hanya angka
            input.value = new Intl.NumberFormat("id-ID").format(value);
        };

        // Mencegah huruf saat mengetik
        document.getElementById("nilai_biaya").addEventListener("keypress", function(e) {
            if (isNaN(e.key) && e.key !== "Backspace") {
                e.preventDefault();
            }
        });
    });

    // account_name
    function updateAccountName() {
        let accountSelect = document.getElementById("account");
        let selectedOption = accountSelect.options[accountSelect.selectedIndex];
        document.getElementById("account_name").value = selectedOption.getAttribute("data-name");
        document.getElementById("account_show").value = selectedOption.getAttribute("data-name");
        document.getElementById("accountId").value = selectedOption.getAttribute("data-id");
    }
</script>
