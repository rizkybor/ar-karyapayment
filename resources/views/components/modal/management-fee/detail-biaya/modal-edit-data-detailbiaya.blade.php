@props(['manfeeDoc', 'detailPaymentId', 'jenis_biaya', 'account_detailbiaya'])

<!-- Modal for Editing Cost Details -->
<div x-data="{ modalOpen: false }">
    <x-button-action class="px-4 py-2 text-white rounded-md" @click="modalOpen = true" color="yellow"
        icon="pencil">Edit</x-button-action>

    <div class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-30 z-50 flex justify-center items-start pt-20"
        x-show="modalOpen" x-cloak>
        <div class="absolute bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full">
            {{-- @click.outside="modalOpen = false"> --}}
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Edit Detail Biaya</h3>

            <form class="text-left"
                action="{{ route('management-fee.detail_payments.update', ['id' => $manfeeDoc->id, 'detail_payment_id' => $detailPaymentId->id]) }}"
                method="POST">
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
                                {{ $detailPaymentId->expense_type == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Account -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        for="account">Account</label>
                    <select id="account" name="account"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                        <option value="">Pilih Akun</option>
                        @foreach ($account_detailbiaya as $akun)
                            <option value="{{ $akun['no'] }}"
                                {{ old('account', $detailPaymentId->account ?? '') == $akun['no'] ? 'selected' : '' }}>
                                ({{ $akun['no'] }})
                                {{ $akun['name'] }}
                            </option>
                        @endforeach
                    </select>

                </div>

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
                    {{-- <input type="text" id="nilai_biaya" name="nilai_biaya"
                        value="{{ $detailPaymentId->nilai_biaya }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required> --}}

                    <x-input id="nilai_biaya" class="block mt-1 w-full" type="text" name="nilai_biaya"
                        value="{{ old('nilai_biaya', number_format($detailPaymentId->nilai_biaya ?? 0, 0, ',', '.')) }}"
                        oninput="formatCurrency(this); calculateValues(); checkChanges()" />
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
        let saveButton = document.getElementById("saveButton");

        if (saveButton) {
            saveButton.disabled = true;
        }

        let initialData = {
            akun: document.getElementById("akun")?.value || '',
            dpp_pekerjaan: document.getElementById("dpp_pekerjaan")?.value.replace(/\./g, '') || '',
            rate_ppn: document.getElementById("rate_ppn")?.value || ''
        };

        window.checkChanges = function() {
            let akunValue = document.getElementById("akun")?.value || '';
            let dppPekerjaanValue = document.getElementById("dpp_pekerjaan")?.value.replace(/\./g, '') ||
                '';
            let ratePpnValue = document.getElementById("rate_ppn")?.value || '';

            let hasChanged = akunValue !== initialData.akun ||
                dppPekerjaanValue !== initialData.dpp_pekerjaan ||
                ratePpnValue !== initialData.rate_ppn;

            if (saveButton) {
                saveButton.disabled = !hasChanged;
                saveButton.classList.toggle("bg-gray-400", !hasChanged);
                saveButton.classList.toggle("cursor-not-allowed", !hasChanged);
                saveButton.classList.toggle("opacity-50", !hasChanged);
                saveButton.classList.toggle("bg-violet-500", hasChanged);
                saveButton.classList.toggle("hover:bg-violet-600", hasChanged);
            }
        };

        window.validateRatePPN = function(input) {
            input.value = input.value.replace(/[^0-9.,]/g, ''); // Hanya angka, koma, titik
            input.value = input.value.replace(/,/g, '.'); // Ubah koma menjadi titik

            let parts = input.value.split('.');
            if (parts.length > 2) {
                input.value = parts[0] + '.' + parts.slice(1).join('');
            }
            checkChanges();
        };

        window.formatCurrency = function(input) {
            let value = input.value.replace(/\D/g, ''); // Hanya angka
            if (value === '') return;
            input.value = new Intl.NumberFormat("id-ID").format(value);
            checkChanges();
        };

        window.calculateValues = function() {
            let dppPekerjaan = parseFloat(document.getElementById("dpp_pekerjaan").value.replace(/\./g,
                '') || 0);
            let ratePpn = parseFloat(document.getElementById("rate_ppn").value.replace(',', '.') || 0);

            let nilaiPpn = (dppPekerjaan * ratePpn) / 100;
            document.getElementById("nilai_ppn").value = new Intl.NumberFormat("id-ID").format(nilaiPpn);

            let jumlah = dppPekerjaan + nilaiPpn;
            document.getElementById("jumlah").value = new Intl.NumberFormat("id-ID").format(jumlah);
        };

        calculateValues(); // Hitung nilai awal
        checkChanges(); // Periksa perubahan awal
    });
</script>
