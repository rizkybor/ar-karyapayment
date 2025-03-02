@props(['nonManfeeDocument', 'akunOptions' => [], 'isEdit' => false])

@php
    $firstAccumulatedCost = $nonManfeeDocument->accumulatedCosts->first();
@endphp

<div class="mt-5 mb-5 md:mt-0 md:col-span-2">
    <div class="flex justify-between items-center mb-3">
        <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
            Akumulasi Biaya
        </h5>
        @if ($isEdit)
        <x-button-action color="violet" icon="save" id="saveButton" disabled onclick="confirmSubmit(event)">
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
                    <select id="akun" name="akun" class="block mt-1 w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 
                        text-sm text-gray-700 dark:text-gray-200 font-medium px-3 py-2 rounded-lg shadow-sm 
                        focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all"
                        onchange="checkChanges()">
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
                        {{ $firstAccumulatedCost->account ?? 'Belum memilih akun' }}
                    </p>
                @endif
            </div>

            {{-- DPP Pekerjaan --}}
            <div class="col-span-1">
                <x-label for="dpp_pekerjaan" value="{{ __('DPP Pekerjaan (Rp)') }}" />
                @if ($isEdit)
                    <x-input id="dpp_pekerjaan" class="block mt-1 w-full" type="text" name="dpp_pekerjaan"
                        value="{{ old('dpp_pekerjaan', number_format($firstAccumulatedCost->dpp ?? 0, 0, ',', '.')) }}"
                        oninput="formatCurrency(this); calculateValues(); checkChanges()" />
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
                        value="{{ old('rate_ppn', number_format($firstAccumulatedCost->rate_ppn ?? 0, 2, '.', '')) }}"
                        oninput="validateRatePPN(this); calculateValues(); checkChanges()" maxlength="5" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">
                        {{ number_format($firstAccumulatedCost->rate_ppn ?? 0, 2, '.', '') }}%
                    </p>
                @endif
            </div>

            {{-- NILAI PPN (Auto) --}}
            <div class="col-span-1 sm:col-span-1">
                <x-label for="nilai_ppn" value="{{ __('NILAI PPN (Rp)') }}" />
                <x-input id="nilai_ppn" class="block mt-1 w-full bg-gray-200 dark:bg-gray-700" type="text"
                    name="nilai_ppn"
                    value="{{ old('nilai_ppn', number_format($firstAccumulatedCost->nilai_ppn ?? 0, 0, ',', '.')) }}"
                    readonly />
            </div>

            {{-- JUMLAH (Auto) --}}
            <div class="col-span-1 sm:col-span-2">
                <x-label for="jumlah" value="{{ __('JUMLAH (Rp)') }}" />
                <x-input id="jumlah" class="block mt-1 w-full bg-gray-200 dark:bg-gray-700" type="text"
                    name="jumlah"
                    value="{{ old('jumlah', number_format($firstAccumulatedCost->total ?? 0, 0, ',', '.')) }}"
                    readonly />
            </div>

        </div>
    </div>
</div>

<!-- SweetAlert Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
     function confirmSubmit(event) {
        event.preventDefault(); // Cegah form terkirim langsung

        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data yang telah diperbarui akan tersimpan.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Simpan!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("accumulatedForm").submit();
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
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
            let dppPekerjaanValue = document.getElementById("dpp_pekerjaan")?.value.replace(/\./g, '') || '';
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
            let dppPekerjaan = parseFloat(document.getElementById("dpp_pekerjaan").value.replace(/\./g, '') || 0);
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