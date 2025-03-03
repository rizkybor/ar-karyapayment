@props(['manfeeDoc', 'jenis_biaya', 'account_dummy']);

<!-- Modal for Adding Cost Details -->
<div x-data="{ modalOpen: false }">
    <x-button-action class="px-4 py-2 bg-violet-500 text-white rounded-md" @click="modalOpen = true">Tambah
        Detail Biaya Personil</x-button-action>
    <div class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 flex items-center justify-center" x-show="modalOpen" x-cloak>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full"
            @click.outside="modalOpen = false">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tambah Detail Biaya</h3>
            <form action="{{ route('management-fee.detail_payments.store', ['id' => $manfeeDoc->id]) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="expense_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis
                        Biaya</label>
                    <select id="expense_type" name="expense_type"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring focus:ring-blue-500">
                        @foreach ($jenis_biaya as $jenis_biayas)
                            <option value="{{ $jenis_biayas }}">{{ $jenis_biayas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="account"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account</label>
                    <select id="account" name="account"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring focus:ring-blue-500">
                        @foreach ($account_dummy as $account_dummys)
                            <option value="{{ $account_dummys }}">{{ $account_dummys }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        for="uraian">Uraian</label>
                    <input type="text" id="uraian" name="uraian"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                </div>
                <!-- Nilai Biaya -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="nilai_biaya">
                        Nilai Biaya
                    </label>
                    <input type="text" id="nilai_biaya" name="formatted_nilai_biaya"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        value="Rp. 0" oninput="formatRupiah(this)" required>
                    <input type="hidden" id="value_hidden" name="nilai_biaya">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md"
                        @click="modalOpen = false">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-violet-500 text-white rounded-md">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('value_hidden').value = 0; // Default Rp. 0
    });

    function formatRupiah(input) {
        let value = input.value.replace(/\D/g, ''); // Hapus semua karakter kecuali angka
        let formatted = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(value);

        input.value = formatted.replace('Rp', 'Rp.').trim(); // Format dengan Rp. di depan
        document.getElementById('value_hidden').value = value || 0; // Hidden input untuk kirim data bersih
    }
</script>
