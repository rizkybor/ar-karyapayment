<div class="mt-5 mb-5 md:mt-0 md:col-span-2">
    <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">
        Akumulasi Biaya
    </h5>
    <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- Akun --}}
            <div class="col-span-1">
                <x-label for="akun" value="{{ __('Akun') }}" />
                @if ($isEdit)
                    <x-input id="akun" class="block mt-1 w-full" type="text" name="akun"
                        value="{{ old('akun', $nonManfeeDocument->akun ?? '') }}" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">{{ $nonManfeeDocument->akun ?? '-' }}</p>
                @endif
            </div>

            {{-- DPP Pekerjaan --}}
            <div class="col-span-1">
                <x-label for="dpp_pekerjaan" value="{{ __('DPP Pekerjaan') }}" />
                @if ($isEdit)
                    <x-input id="dpp_pekerjaan" class="block mt-1 w-full" type="text" name="dpp_pekerjaan"
                        value="{{ old('dpp_pekerjaan', $nonManfeeDocument->dpp_pekerjaan ?? '') }}" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">{{ $nonManfeeDocument->dpp_pekerjaan ?? '-' }}</p>
                @endif
            </div>

            {{-- RATE PPN --}}
            <div class="col-span-1 sm:col-span-1">
                <x-label for="rate_ppn" value="{{ __('RATE PPN') }}" />
                @if ($isEdit)
                    <x-input id="rate_ppn" class="block mt-1 w-full" type="text" name="rate_ppn"
                        value="{{ old('rate_ppn', $nonManfeeDocument->rate_ppn ?? '') }}" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">{{ $nonManfeeDocument->rate_ppn ?? '-' }}</p>
                @endif
            </div>

            {{-- NILAI PPN --}}
            <div class="col-span-1 sm:col-span-1">
                <x-label for="nilai_ppn" value="{{ __('NILAI PPN') }}" />
                @if ($isEdit)
                    <x-input id="nilai_ppn" class="block mt-1 w-full" type="text" name="nilai_ppn"
                        value="{{ old('nilai_ppn', $nonManfeeDocument->nilai_ppn ?? '') }}" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">{{ $nonManfeeDocument->nilai_ppn ?? '-' }}</p>
                @endif
            </div>

            {{-- JUMLAH --}}
            <div class="col-span-1 sm:col-span-2">
                <x-label for="jumlah" value="{{ __('JUMLAH') }}" />
                @if ($isEdit)
                    <x-input id="jumlah" class="block mt-1 w-full" type="text" name="jumlah"
                        value="{{ old('jumlah', $nonManfeeDocument->jumlah ?? '') }}" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">{{ $nonManfeeDocument->jumlah ?? '-' }}</p>
                @endif
            </div>

        </div>
    </div>
</div>