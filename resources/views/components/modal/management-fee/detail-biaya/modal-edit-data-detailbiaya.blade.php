@props(['manfeeDoc'])

<!-- Modal for Adding Cost Details -->
<div x-data="{ modalOpen: false }">
    <x-button-action class="px-4 py-2 bg-yellow-500 text-white rounded-md"
        @click="modalOpen = true">Edit</x-button-action>
    <div class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-30 z-50 flex justify-center items-start pt-20"
        x-show="modalOpen" x-cloak>
        <div class="absolute bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full"
            @click.outside="modalOpen = false">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Edit Detail Biaya</h3>
            <form class="text-left" action="{{ route('management-fee.detail_payments.store', ['id' => $manfeeDoc->id]) }}"
                method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 items-start"
                        for="expense_type">Jenis
                        Biaya</label>
                    <select id="expense_type" name="expense_type"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                        <option value="" disabled>Pilih Jenis Biaya</option>
                        <option value="biaya_personil">Biaya Personil</option>
                        <option value="biaya_non_personil">Biaya Non Personil</option>
                        <option value="biaya_lembur">Biaya Lembur</option>
                        <option value="thr">THR</option>
                        <option value="kompensasi">Kompensasi</option>
                        <option value="sppd">SPPD</option>
                        <option value="add_cost">Add Cost</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        for="account">Account</label>
                    <select id="account" name="account"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                        <option value="">Pilih Akun</option>
                        <option value="10011">10011</option>
                        <option value="10012">10012</option>
                        <option value="10013">10013</option>
                        <option value="10014">10014</option>
                        <option value="10015">10015</option>
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
