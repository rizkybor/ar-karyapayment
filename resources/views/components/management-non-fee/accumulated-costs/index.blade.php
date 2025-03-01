@props(['nonManfeeDocument', 'akunOptions' => [], 'isEdit' => false])

<div class="mt-5 mb-5 md:mt-0 md:col-span-2">
    <div class="flex justify-between items-center mb-3">
        <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
            Akumulasi Biaya
        </h5>
        @if ($isEdit == true)
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
                            <option value="{{ $akun }}" {{ old('akun', $nonManfeeDocument->akun ?? '') == $akun ? 'selected' : '' }}>
                                {{ $akun }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <p class="text-gray-800 dark:text-gray-200">{{ $nonManfeeDocument->akun ?? '-' }}</p>
                @endif
            </div>

            {{-- DPP Pekerjaan --}}
            <div class="col-span-1">
                <x-label for="dpp_pekerjaan" value="{{ __('DPP Pekerjaan') }}" />
                @if ($isEdit)
                    <x-input id="dpp_pekerjaan" class="block mt-1 w-full" type="text" name="dpp_pekerjaan"
                        value="{{ old('dpp_pekerjaan', number_format($nonManfeeDocument->dpp_pekerjaan ?? 0, 2, ',', '.')) }}" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">
                        {{ number_format($nonManfeeDocument->dpp_pekerjaan ?? 0, 2, ',', '.') }}
                    </p>
                @endif
            </div>

            {{-- RATE PPN --}}
            <div class="col-span-1 sm:col-span-1">
                <x-label for="rate_ppn" value="{{ __('RATE PPN') }}" />
                @if ($isEdit)
                    <x-input id="rate_ppn" class="block mt-1 w-full" type="text" name="rate_ppn"
                        value="{{ old('rate_ppn', number_format($nonManfeeDocument->rate_ppn ?? 0, 2, ',', '.')) }}" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">
                        {{ number_format($nonManfeeDocument->rate_ppn ?? 0, 2, ',', '.') }}
                    </p>
                @endif
            </div>

            {{-- NILAI PPN --}}
            <div class="col-span-1 sm:col-span-1">
                <x-label for="nilai_ppn" value="{{ __('NILAI PPN') }}" />
                @if ($isEdit)
                    <x-input id="nilai_ppn" class="block mt-1 w-full" type="text" name="nilai_ppn"
                        value="{{ old('nilai_ppn', number_format($nonManfeeDocument->nilai_ppn ?? 0, 2, ',', '.')) }}" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">
                        {{ number_format($nonManfeeDocument->nilai_ppn ?? 0, 2, ',', '.') }}
                    </p>
                @endif
            </div>

            {{-- JUMLAH --}}
            <div class="col-span-1 sm:col-span-2">
                <x-label for="jumlah" value="{{ __('JUMLAH') }}" />
                @if ($isEdit)
                    <x-input id="jumlah" class="block mt-1 w-full" type="text" name="jumlah"
                        value="{{ old('jumlah', number_format($nonManfeeDocument->jumlah ?? 0, 2, ',', '.')) }}" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">
                        {{ number_format($nonManfeeDocument->jumlah ?? 0, 2, ',', '.') }}
                    </p>
                @endif
            </div>

        </div>
    </div>
</div>