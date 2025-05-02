@props(['nonManfeeDocument', 'optionAccount' => [], 'isEdit' => false])

@php
    $firstAccumulatedCost = $nonManfeeDocument->accumulatedCosts->first();
@endphp

<div class="mt-5 mb-5 md:mt-0 md:col-span-2">
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

    <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- Akun (Dropdown Custom Sesuai Permintaan) --}}
            <div class="flex flex-col">

                {{-- <div class="col-span-1">
                    <x-label for="akun" value="{{ __('Akun') }}" />
                    <select id="akun" name="akun"
                        class="block mt-1 w-full bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200 
                        font-medium px-3 py-2 rounded-lg shadow-sm focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all
                        {{ !$isEdit ? 'border-transparent bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : 'border-gray-300 dark:border-gray-600' }}"
                        onchange="checkChanges()" {{ !$isEdit ? 'disabled' : '' }}>
                        <option value="">Pilih Akun</option>
                        @foreach ($optionAccount as $akun)
                            <option value="{{ $akun['no'] }}" data-name="{{ $akun['name'] }}"
                                {{ old('akun', $firstAccumulatedCost->account ?? '') == $akun['no'] ? 'selected' : '' }}>
                                ({{ $akun['no'] }})
                                {{ $akun['name'] }}
                            </option>
                        @endforeach
                    </select>
    
                    <input type="hidden" id="nama_akun" name="nama_akun"
                        value="{{ old('nama_akun', $firstAccumulatedCost->account_name ?? '') }}">
                </div> --}}

                <x-label for="akunInput" value="{{ __('Akun') }}" />

                @if ($isEdit)
                    <div class="relative mt-1">
                        {{-- Input untuk mengetik nama akun --}}
                        <input type="text" id="akunInput" placeholder="Cari/Pilih Barang & Jasa..."
                            value="{{ old('nama_akun', $firstAccumulatedCost->account_name ?? '') }}{{ optional($firstAccumulatedCost)->account ? ' (' . optional($firstAccumulatedCost)->account . ')' : '' }}"
                            class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md 
           bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-200 
           focus:outline-none focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all"
                            oninput="filterDropdown()" onclick="toggleDropdown()" autocomplete="off" />

                        {{-- Dropdown List --}}
                        <ul id="akunDropdown"
                            class="absolute z-10 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 
                                   rounded-md mt-1 max-h-60 overflow-auto shadow-md hidden transition-all">
                            @foreach ($optionAccount as $akun)
                                <li class="p-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                                    onclick="selectAkun('{{ $akun['no'] }}', '{{ $akun['name'] }}')">
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $akun['name'] }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $akun['no'] }}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Hidden input untuk menyimpan kode dan nama akun --}}
                    <input type="hidden" name="akun" id="akunHidden"
                        value="{{ old('akun', $firstAccumulatedCost->account ?? '') }}">
                    <input type="hidden" id="nama_akun" name="nama_akun"
                        value="{{ old('nama_akun', $firstAccumulatedCost->account_name ?? '') }}">
                @else
                    {{-- View Mode, hanya tampilkan teks --}}
                    <p class="text-gray-800 dark:text-gray-200 mt-2">
                        {{ $firstAccumulatedCost->account_name ?? '' }}
                        @if (!empty($firstAccumulatedCost->account))
                            ({{ $firstAccumulatedCost->account }})
                        @endif
                    </p>
                @endif
            </div>

            {{-- DPP Pekerjaan --}}
            <div class="flex flex-col">
                <x-label for="dpp_pekerjaan" value="{{ __('DPP Pekerjaan (Rp)') }}" />

                @if ($isEdit)
                    <x-input id="dpp_pekerjaan" class="block mt-1 w-full" type="text" name="dpp_pekerjaan"
                        value="{{ old('dpp_pekerjaan', number_format($firstAccumulatedCost->dpp ?? 0, 0, ',', '.')) }}"
                        oninput="formatCurrency(this); calculateValues(); checkChanges()" />
                @else
                    <p class="text-gray-800 dark:text-gray-200 mt-2">
                        Rp. {{ number_format($firstAccumulatedCost->dpp ?? 0, 0, ',', '.') }}
                    </p>
                @endif
            </div>
        </div>

        <br />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- RATE PPN --}}
            <div class="col-span-1 sm:col-span-1">
                <x-label for="rate_ppn" value="{{ __('Rate PPN (%)') }}" />
                @if ($isEdit)
                    <x-input id="rate_ppn" class="block mt-1 w-full" type="text" name="rate_ppn"
                        value="{{ old('rate_ppn', number_format($firstAccumulatedCost->rate_ppn ?? 0, 2, '.', '')) }}"
                        oninput="validateRatePPN(this); calculateValues(); checkChanges()" maxlength="5" />

                    <x-input id="comment_ppn" class="block mt-1 w-full" placeholder="Keterangan PPN" name="comment_ppn"
                        value="{{ old('comment_ppn', $firstAccumulatedCost->comment_ppn ?? '') }}" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">
                        {{ number_format($firstAccumulatedCost->rate_ppn ?? 0, 2, '.', '') }}%
                    </p>
                @endif
            </div>

            {{-- NILAI PPN (Auto) --}}
            <div class="col-span-1 sm:col-span-1">
                <x-label for="nilai_ppn" value="{{ __('Nilai PPN (Rp)') }}" />
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
                <x-label for="jumlah" value="{{ __('Total Akumulasi Biaya (Rp)') }}" />
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let saveButton = document.getElementById("saveButton");

        if (saveButton) {
            saveButton.disabled = true;
        }

        let akunNo = document.getElementById("akunHidden")?.value || '';
        let akunName = document.getElementById("nama_akun")?.value || '';

        let initialData = {
            akun: akunNo,
            nama_akun: `${akunName} (${akunNo})`, // harus cocok dengan isi input akunInput
            dpp_pekerjaan: document.getElementById("dpp_pekerjaan")?.value.replace(/\./g, '') || '',
            rate_ppn: document.getElementById("rate_ppn")?.value || '',
            comment_ppn: document.getElementById("comment_ppn")?.value || '',
            nilai_ppn: document.getElementById("nilai_ppn")?.value.replace(/\./g, '') || '',
            jumlah: document.getElementById("jumlah")?.value.replace(/\./g, '') || ''
        };

        window.checkChanges = function() {
    let akunValue = document.getElementById("akunHidden")?.value || '';
    let akunNameFull = `${document.getElementById("nama_akun")?.value || ''} (${akunValue})`;

    let ratePpnValue = document.getElementById("rate_ppn")?.value || '';
    let commentPpnValue = document.getElementById("comment_ppn")?.value || '';
    let nilaiPpnValue = document.getElementById("nilai_ppn")?.value.replace(/\./g, '') || '';
    let jumlahValue = document.getElementById("jumlah")?.value.replace(/\./g, '') || '';

    let dppPekerjaan = document.getElementById("dpp_pekerjaan")?.value.replace(/\./g, '') || '';

    let hasChanged =
        akunValue !== initialData.akun ||
        akunName !== initialData.nama_akun ||
        dppPekerjaan !== initialData.dpp_pekerjaan ||
        ratePpnValue !== initialData.rate_ppn ||
        commentPpnValue !== initialData.comment_ppn ||
        nilaiPpnValue !== initialData.nilai_ppn ||
        jumlahValue !== initialData.jumlah;

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
            input.value = input.value.replace(/[^0-9.,]/g, '');
            input.value = input.value.replace(/,/g, '.');

            let parts = input.value.split('.');
            if (parts.length > 2) {
                input.value = parts[0] + '.' + parts.slice(1).join('');
            }
            checkChanges();
        };

        window.formatCurrency = function(input) {
            let value = input.value.replace(/\D/g, '');
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

        calculateValues();
        checkChanges();
    });

    function toggleDropdown() {
        const isEdit = {{ $isEdit ? 'true' : 'false' }};
        if (!isEdit) return; // Kalau View, jangan buka dropdown

        document.getElementById('akunDropdown').classList.toggle('hidden');
    }

    function selectAkun(no, name) {
        document.getElementById('akunInput').value = `${name} (${no})`;
        document.getElementById('akunHidden').value = no;
        document.getElementById('nama_akun').value = name;
        document.getElementById('akunDropdown').classList.add('hidden');
        checkChanges();
    }

    function filterDropdown() {
        const input = document.getElementById('akunInput').value.toLowerCase();
        const items = document.querySelectorAll('#akunDropdown li');

        items.forEach(item => {
            const text = item.innerText.toLowerCase();
            item.style.display = text.includes(input) ? 'block' : 'none';
        });
    }

    // Close dropdown kalau klik di luar input dan dropdown
    document.addEventListener('click', function(event) {
        const input = document.getElementById('akunInput');
        const dropdown = document.getElementById('akunDropdown');
        if (!input.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
