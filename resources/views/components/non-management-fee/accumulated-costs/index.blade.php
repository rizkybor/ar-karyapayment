@props(['nonManfeeDocument', 'akunOptions' => [], 'isEdit' => false])

@php
    // Ambil akumulasi biaya pertama
    $firstAccumulatedCost = $nonManfeeDocument->accumulatedCosts->first();
@endphp

<form method="POST"
    action="{{ route('non-management-fee.accumulated.' . ($firstAccumulatedCost ? 'update' : 'store'), [
        'id' => $nonManfeeDocument->id,
        'accumulated_id' => $firstAccumulatedCost->id ?? null,
    ]) }}">
    @csrf
    @if ($firstAccumulatedCost)
        @method('PUT')
    @endif

    <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
        <div class="flex justify-between items-center mb-3">
            <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                Akumulasi Biaya
            </h5>
            @if ($isEdit)
                <x-button-action color="violet" type="submit">
                    Simpan Akumulasi Biaya
                </x-button-action>
            @endif
        </div>

        <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                {{-- Akun (Dropdown) --}}
                <div class="col-span-1">
                    <x-label for="akun" value="{{ __('Akun') }}" />
                    @if ($isEdit)
                        <select id="akun" name="akun"
                            class="block mt-1 w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 
                            text-sm text-gray-700 dark:text-gray-200 font-medium px-3 py-2 rounded-lg shadow-sm 
                            focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all">
                            <option value="">Pilih Akun</option>
                            @foreach ($akunOptions as $akun)
                                <option value="{{ $akun }}"
                                    {{ old('akun', $firstAccumulatedCost->account ?? '') == $akun ? 'selected' : '' }}>
                                    {{ $akun }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <p class="text-gray-800 dark:text-gray-200">
                            {{ $firstAccumulatedCost->account == '' ?? 'Belum memilih akun' }}
                        </p>
                    @endif
                </div>

                {{-- DPP Pekerjaan --}}
                <div class="col-span-1">
                    <x-label for="dpp_pekerjaan" value="{{ __('DPP Pekerjaan (Rp)') }}" />
                    @if ($isEdit)
                        <x-input id="dpp_pekerjaan" class="block mt-1 w-full" type="text" name="dpp_pekerjaan"
                            value="{{ old('dpp_pekerjaan', number_format($firstAccumulatedCost->dpp ?? 0, 0, ',', '.')) }}"
                            oninput="formatCurrency(this); calculateValues()" />
                    @else
                        <p class="text-gray-800 dark:text-gray-200">
                            Rp. {{ number_format($firstAccumulatedCost->dpp ?? 0, 0, ',', '.') }}
                        </p>
                    @endif
                </div>

                {{-- RATE PPN --}}
                <div class="col-span-1 sm:col-span-1">
                    <x-label for="rate_ppn" value="{{ __('RATE PPN (%)') }}" />
                    @if ($isEdit)
                        <x-input id="rate_ppn" class="block mt-1 w-full" type="text" name="rate_ppn"
                            value="{{ old('rate_ppn', intval($firstAccumulatedCost->rate_ppn ?? 0)) }}"
                            oninput="validateRatePPN(this); calculateValues()" maxlength="3" />
                    @else
                        <p class="text-gray-800 dark:text-gray-200">
                            {{ intval($firstAccumulatedCost->rate_ppn ?? 0) }}%
                        </p>
                    @endif
                </div>

                {{-- NILAI PPN (Auto) --}}
                <div class="col-span-1 sm:col-span-1">
                    <x-label for="nilai_ppn" value="{{ __('NILAI PPN (Rp)') }}" />
                    @if ($isEdit)
                        <x-input id="nilai_ppn" class="block mt-1 w-full bg-gray-200 dark:bg-gray-700" type="text"
                            name="nilai_ppn"
                            value="{{ old('nilai_ppn', number_format($firstAccumulatedCost->nilai_ppn ?? 0, 0, ',', '.')) }}"
                            readonly />
                    @else
                        <p class="text-gray-800 dark:text-gray-200">
                            Rp. {{ number_format($firstAccumulatedCost->nilai_ppn ?? 0, 0, ',', '.') }}
                        </p>
                    @endif
                </div>

                {{-- JUMLAH (Auto) --}}
                <div class="col-span-1 sm:col-span-2">
                    <x-label for="jumlah" value="{{ __('JUMLAH (Rp)') }}" />
                    @if ($isEdit)
                        <x-input id="jumlah" class="block mt-1 w-full bg-gray-200 dark:bg-gray-700" type="text"
                            name="jumlah"
                            value="{{ old('jumlah', number_format($firstAccumulatedCost->total ?? 0, 0, ',', '.')) }}"
                            readonly />
                    @else
                        <p class="text-gray-800 dark:text-gray-200 text-2xl font-bold">
                            Rp. {{ number_format($firstAccumulatedCost->total ?? 0, 0, ',', '.') }}
                        </p>
                    @endif
                </div>

            </div>
        </div>
    </div>
</form>

<script>
    function validateRatePPN(input) {
        // Hapus semua karakter kecuali angka
        input.value = input.value.replace(/\D/g, '');

        // Batasi maksimal 3 digit
        if (input.value.length > 3) {
            input.value = input.value.slice(0, 3);
        }

        // Hitung ulang nilai setelah validasi
        calculateValues();
    }

    function formatCurrency(input) {
        // Hapus semua karakter selain angka
        let value = input.value.replace(/\D/g, '');

        // Format angka ke bentuk ribuan dengan separator koma
        input.value = new Intl.NumberFormat("id-ID").format(value);
    }

    function calculateValues() {
        let dppPekerjaan = document.getElementById("dpp_pekerjaan").value.replace(/\./g, '') || 0;
        let ratePpn = document.getElementById("rate_ppn").value || 0;

        dppPekerjaan = parseInt(dppPekerjaan);
        ratePpn = parseInt(ratePpn);

        // Hitung Nilai PPN
        let nilaiPpn = (dppPekerjaan * ratePpn) / 100;
        document.getElementById("nilai_ppn").value = new Intl.NumberFormat("id-ID").format(nilaiPpn);

        // Hitung Jumlah
        let jumlah = dppPekerjaan + nilaiPpn;
        document.getElementById("jumlah").value = new Intl.NumberFormat("id-ID").format(jumlah);
    }
</script>
