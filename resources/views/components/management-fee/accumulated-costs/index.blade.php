@props([
    'manfeeDoc',
    'subtotals',
    'subtotalBiayaNonPersonil',
    'rate_manfee' => [],
    'account_akumulasi' => [],
    'isEdit' => false,
])

@php
    $firstAccumulatedCost = $manfeeDoc->accumulatedCosts->first();
@endphp

{{-- Akumulasi Biaya --}}

{{-- Tittle Akumulasi Biaya --}}
<div class="mt-5 mb-5 md:mt-0 md:col-span-2">
    <!-- Left: Title -->
    <div class="flex justify-between items-center mb-3">
        <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
            Akumulasi Biaya
        </h5>
        @if ($isEdit)
            <x-button-action icon="save" id="saveButton" disabled="true"
                onclick="openConfirmationModal(
        'Konfirmasi Simpan',
        'Yakin ingin menyimpan perubahan?',
        () => document.getElementById('accumulatedForm').submit()
    )">
                Simpan Akumulasi Biaya
            </x-button-action>
        @endif
    </div>
</div>

{{-- Tittle Akumulasi Biaya End --}}
<div class="mt-5 md:mt-0 md:col-span-2 mb-8">
    <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        {{-- Akumulasi Biaya Form --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">

            {{-- Akun --}}
            <div class="col-span-1 sm:col-span-2 lg:col-span-3">
                <x-label for="account" value="{{ __('Akun') }}" />
                @if ($isEdit)
                    <select id="account_id" name="account"
                        class="block mt-1 w-full bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200 
                        font-medium px-3 py-2 rounded-lg shadow-sm focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all
                        {{ !$isEdit ? 'border-transparent bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : 'border-gray-300 dark:border-gray-600' }}"
                        onchange="document.getElementById('account_name_input').value = this.selectedOptions[0].dataset.name"
                        {{ !$isEdit ? 'disabled' : '' }}>
                        @foreach ($account_akumulasi as $akun)
                            <option value="{{ $akun['no'] }}" data-name="{{ $akun['name'] }}"
                                {{ old('account', $firstAccumulatedCost->account ?? '') == $akun['no'] ? 'selected' : '' }}>
                                ({{ $akun['no'] }})
                                {{ $akun['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" id="account_name_input" name="account_name"
                        value="{{ old('account_name', $firstAccumulatedCost->account_name ?? '') }}">
                @else
                    <p class="text-gray-800 dark:text-gray-200">
                        @if ($firstAccumulatedCost->account)
                            ({{ $firstAccumulatedCost->account }}) {{ $firstAccumulatedCost->account_name }}
                        @else
                            Belum memilih akun
                        @endif
                    </p>
                @endif
            </div>

            {{-- Rate Manfee --}}
            <div class="col-span-1 sm:col-span-2 lg:col-span-2 lg:col-start-4">
                <x-label for="total_expense_manfee" value="{{ __('Rate Manfee (%)') }}" />
                @if ($isEdit)
                    {{-- <select id="total_expense_manfee" name="total_expense_manfee"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200 font-medium px-3 py-2 rounded-lg shadow-sm focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all"
                        onchange="calculateManfee()">
                        <option value="">Rate Manfee</option>
                        @foreach ($rate_manfee as $rate_manfees)
                            <option value="{{ $rate_manfees }}"
                                {{ old('rate_manfees', $firstAccumulatedCost->total_expense_manfee ?? '') == $rate_manfees ? 'selected' : '' }}>
                                {{ $rate_manfees }}
                            </option>
                        @endforeach
                    </select> --}}

                    <x-input id="total_expense_manfee" class="block mt-1 w-full" type="text"
                        name="total_expense_manfee"
                        value="{{ old('total_expense_manfee', number_format($firstAccumulatedCost->total_expense_manfee ?? 0, 0, ',', '.')) }}"
                        onchange="calculateManfee();  checkChanges()" maxlength="5" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">
                        {{ number_format($firstAccumulatedCost->total_expense_manfee ?? null, 0, ',', '.') }}%
                    </p>
                @endif

            </div>

            {{-- Nilai Manfee (Disabled) --}}
            <div class="col-span-1 sm:col-span-2 lg:col-span-3 lg:col-start-6">
                <x-label for="nilai_manfee" value="{{ __('Nilai Manfee') }}" />
                @if ($isEdit)
                    <x-input id="nilai_manfee" class="block mt-1 w-full" type="text" name="nilai_manfee"
                        value="Rp. {{ old('nilai_manfee', number_format($firstAccumulatedCost->nilai_manfee ?? null, 0, ',', '.')) }}"
                        oninput="formatCurrency(this);" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">Rp.
                        {{ number_format($firstAccumulatedCost->nilai_manfee ?? null, 0, ',', '.') }}</p>
                @endif
            </div>

            {{-- DPP Disabled --}}
            <div class="col-span-1 sm:col-span-3 lg:col-span-5 lg:row-start-2">
                <x-label for="dpp" value="{{ __('DPP') }}" />
                @if ($isEdit)
                    <x-input id="dpp" class="block mt-1 w-full bg-gray-200 dark:bg-gray-700" type="text"
                        name="dpp"
                        value="Rp. {{ old('dpp', number_format($firstAccumulatedCost->dpp ?? null, 0, ',', '.')) }}"
                        onchange="calculateDpp()" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">
                        Rp. {{ number_format($firstAccumulatedCost->dpp ?? null, 0, ',', '.') }}</p>
                @endif

            </div>

            {{-- Rate PPN --}}
            <div class="col-span-1 sm:col-span-3 lg:col-span-5 lg:row-start-3">
                <x-label for="rate_ppn" value="{{ __('Rate PPN (%)') }}" />
                @if ($isEdit)
                    <x-input id="rate_ppn" class="block mt-1 w-full" type="text" name="rate_ppn"
                        value="{{ old('rate_ppn', number_format($firstAccumulatedCost->rate_ppn ?? null, 0, ',', '.')) }}"
                        oninput="validateRatePPN(this); calculateValues(); checkChanges()" maxlength="5" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">
                        {{ number_format($firstAccumulatedCost->rate_ppn ?? null, 0, ',', '.') }} %
                    </p>
                @endif
            </div>

            {{-- Jumlah --}}
            <div class="col-span-1 sm:col-span-2 lg:col-span-5 lg:col-start-1 lg:row-start-4">
                <x-label for="total" value="{{ __('Jumlah') }}" />
                @if ($isEdit)
                    <x-input id="total" class="block mt-1 w-full bg-gray-200 dark:bg-gray-700" type="text"
                        name="total"
                        value="Rp. {{ old('total', number_format($firstAccumulatedCost->total ?? null, 0, ',', '.')) }}"
                        readonly />
                @else
                    <p class="text-gray-800 dark:text-gray-200 text-2xl font-bold">Rp.
                        {{ number_format($firstAccumulatedCost->total ?? null, 0, ',', '.') }}</p>
                @endif

            </div>

            {{-- Nilai PPN (Disabled) --}}
            <div class="col-span-1 sm:col-span-2 lg:col-span-3 lg:col-start-6 lg:row-start-3">
                <x-label for="nilai_ppn" value="{{ __('NILAI PPN') }}" />
                @if ($isEdit)
                    <x-input id="nilai_ppn" class="block mt-1 w-full bg-gray-200 dark:bg-gray-700" type="text"
                        name="nilai_ppn"
                        value="Rp. {{ old('nilai_ppn', number_format($firstAccumulatedCost->nilai_ppn ?? null, 0, ',', '.')) }}"
                        readonly />
                @else
                    <p class="text-gray-800 dark:text-gray-200">Rp.
                        {{ number_format($firstAccumulatedCost->nilai_ppn ?? null, 0, ',', '.') }}</p>
                @endif
            </div>
        </div>
        {{-- Akumulasi Biaya Form End --}}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let saveButton = document.getElementById("saveButton");

        // Simpan nilai awal untuk mendeteksi perubahan input
        let initialValues = {
            nilai_manfee: document.getElementById('nilai_manfee').value,
            account: document.getElementById('account_id').value,
            total_expense_manfee: document.getElementById('total_expense_manfee').value,
            dpp: document.getElementById('dpp').value,
            rate_ppn: document.getElementById('rate_ppn').value
        };

        // Fungsi untuk mengecek apakah ada perubahan nilai dari nilai awal
        function hasChanges() {
            return (
                document.getElementById('nilai_manfee').value !== initialValues.nilai_manfee ||
                document.getElementById('account_id').value !== initialValues.account ||
                document.getElementById('total_expense_manfee').value !== initialValues
                .total_expense_manfee ||
                document.getElementById('dpp').value !== initialValues.dpp ||
                document.getElementById('rate_ppn').value !== initialValues.rate_ppn
            );
        }

        // Fungsi untuk memperbarui status tombol save
        function updateSaveButtonState() {
            let hasChanged = hasChanges();
            if (saveButton) {
                saveButton.disabled = !hasChanged;
                saveButton.classList.toggle("bg-gray-400", !hasChanged);
                saveButton.classList.toggle("cursor-not-allowed", !hasChanged);
                saveButton.classList.toggle("opacity-50", !hasChanged);
                saveButton.classList.toggle("bg-violet-500", hasChanged);
                saveButton.classList.toggle("hover:bg-violet-600", hasChanged);
            }
        }

        // Fungsi untuk memformat angka ke dalam format Rupiah
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(angka);
        }

        // Fungsi untuk menghapus format Rupiah dan mengubahnya kembali ke angka
        function unformatRupiah(rupiah) {
            return parseFloat(rupiah.replace(/[^0-9,-]/g, '').replace(',', '.')) || 0;
        }

        // Fungsi untuk menghitung DPP (Dasar Pengenaan Pajak)
        function calculateDpp(nilaiManfee) {
            let subtotalBiayaNonPersonil = {{ $subtotalBiayaNonPersonil }}; // Ambil nilai subtotal dari server
            let dpp = nilaiManfee + subtotalBiayaNonPersonil;
            document.getElementById('dpp').value = formatRupiah(dpp);
            calculatePPN(dpp); // Hitung nilai PPN berdasarkan DPP
            calculateTotal(); // Hitung total keseluruhan
            updateSaveButtonState(); // Perbarui status tombol save
        }

        // Fungsi untuk menghitung nilai PPN berdasarkan DPP
        function calculatePPN(dpp) {
            let ratePpn = parseFloat(document.getElementById('rate_ppn').value) || 0;
            document.getElementById('nilai_ppn').value = formatRupiah(dpp * (ratePpn / 100));
        }

        // Fungsi untuk menghitung nilai Manfee berdasarkan subtotal
        function calculateManfee() {
            let subtotals = {{ $subtotals->sum() }}; // Ambil nilai subtotal dari server
            let rateManfee = parseFloat(document.getElementById('total_expense_manfee').value) || 0;
            let nilaiManfee = subtotals * (rateManfee / 100);
            document.getElementById('nilai_manfee').value = formatRupiah(nilaiManfee);
            calculateDpp(nilaiManfee); // Hitung DPP setelah nilai Manfee diperbarui
        }

        // Fungsi untuk menghitung total keseluruhan biaya
        function calculateTotal() {
            let subtotals = {{ $subtotals->sum() }};
            let subtotalBiayaNonPersonil = {{ $subtotalBiayaNonPersonil }};
            let nilaiManfee = unformatRupiah(document.getElementById('nilai_manfee').value);
            let nilaiPpn = unformatRupiah(document.getElementById('nilai_ppn').value);
            document.getElementById('total').value = formatRupiah(subtotals + nilaiManfee + nilaiPpn +
                subtotalBiayaNonPersonil);
        }

        // Event listener untuk mengupdate nilai saat total_expense_manfee berubah
        document.getElementById('total_expense_manfee').addEventListener('change', function() {
            calculateManfee();
            updateSaveButtonState();
        });

        // Event listener untuk mengupdate nilai saat rate_ppn berubah
        document.getElementById('rate_ppn').addEventListener('input', function() {
            let dpp = unformatRupiah(document.getElementById('dpp').value);
            calculatePPN(dpp);
            calculateTotal();
            updateSaveButtonState();
        });

        // Event listener untuk mengupdate status tombol save ketika account atau DPP berubah
        document.getElementById('account_id').addEventListener('change', updateSaveButtonState);
        document.getElementById('dpp').addEventListener('input', updateSaveButtonState);

        document.getElementById('nilai_manfee').addEventListener('input', function() {
            let nilaiManfee = unformatRupiah(this.value);
            calculateDpp(nilaiManfee); // Hitung ulang DPP berdasarkan nilai Manfee
            updateSaveButtonState();
        });


        // Event listener untuk memformat angka sebelum data dikirim ke server
        document.getElementById('accumulatedForm').addEventListener('submit', function() {
            document.getElementById('nilai_manfee').value = unformatRupiah(document.getElementById(
                'nilai_manfee').value);
            document.getElementById('dpp').value = unformatRupiah(document.getElementById('dpp').value);
            document.getElementById('nilai_ppn').value = unformatRupiah(document.getElementById(
                'nilai_ppn').value);
            document.getElementById('total').value = unformatRupiah(document.getElementById('total')
                .value);
        });

        // Inisialisasi status tombol save pada awal halaman dimuat
        updateSaveButtonState();
    });
</script>
