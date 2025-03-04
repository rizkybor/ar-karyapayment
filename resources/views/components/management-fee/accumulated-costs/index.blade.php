@props([
    'manfeeDoc',
    'subtotals',
    'subtotalBiayaNonPersonil',
    'rate_manfee' => [],
    'account_dummy' => [],
    'isEdit' => false,
])

@php
    // dd(number_format($subtotals->sum()));
    $firstAccumulatedCost = $manfeeDoc->accumulatedCosts->first();
@endphp



{{-- Akumulasi Biaya --}}

{{-- Tittle Akumulasi Biaya --}}
<div class="sm:flex sm:justify-between sm:items-center mb-4">
    <!-- Left: Title -->
    <div class="mb-4 sm:mb-0">
        <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
            Akumulasi Biaya
        </h5>
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
                    <select id="account" name="account"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 
                        text-sm text-gray-700 dark:text-gray-200 font-medium px-3 py-2 rounded-lg shadow-sm 
                        focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all"
                        onchange="checkChanges()">
                        <option value="">Pilih Akun</option>
                        @foreach ($account_dummy as $akun)
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

            {{-- Rate Manfee --}}
            <div class="col-span-1 sm:col-span-2 lg:col-span-2 lg:col-start-4">
                <x-label for="total_expense_manfee" value="{{ __('Rate Manfee') }}" />
                @if ($isEdit)
                    <select id="total_expense_manfee" name="total_expense_manfee"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 
                        text-sm text-gray-700 dark:text-gray-200 font-medium px-3 py-2 rounded-lg shadow-sm 
                        focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all"
                        onchange="checkChanges()">
                        <option value="">Rate Manfee</option>
                        @foreach ($rate_manfee as $rate_manfees)
                            <option value="{{ $rate_manfees }}"
                                {{ old('rate_manfees', $firstAccumulatedCost->rate_manfees ?? '') == $rate_manfees ? 'selected' : '' }}>
                                {{ $rate_manfees }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <p class="text-gray-800 dark:text-gray-200">
                        {{ $firstAccumulatedCost->rate_manfees ?? 'Belum memilih akun' }}
                    </p>
                @endif
            </div>

            {{-- Nilai Manfee (Disabled) --}}
            <div class="col-span-1 sm:col-span-2 lg:col-span-3 lg:col-start-6">
                <x-label for="nilai_manfee" value="{{ __('Nilai Manfee') }}" />
                @if ($isEdit)
                    <x-input id="nilai_manfee" class="block mt-1 w-full bg-gray-200 dark:bg-gray-700" type="text"
                        name="nilai_manfee" value="{{ old('nilai_manfee', $manfeeDoc->nilai_manfee ?? '') }}"
                        readonly />
                @else
                    <p class="text-gray-800 dark:text-gray-200">{{ $manfeeDoc->nilai_manfee ?? '-' }}</p>
                @endif
            </div>

            {{-- DPP Disabled --}}
            <div class="col-span-1 sm:col-span-3 lg:col-span-5 lg:row-start-2">
                <x-label for="dpp" value="{{ __('DPP') }}" />
                @if ($isEdit)
                    <x-input id="dpp" class="block mt-1 w-full bg-gray-200 dark:bg-gray-700" type="text"
                        name="dpp" value="{{ old('dpp', $manfeeDoc->dpp ?? '') }}" readonly />
                @else
                    <p class="text-gray-800 dark:text-gray-200">{{ $manfeeDoc->dpp ?? '-' }}</p>
                @endif
            </div>

            {{-- Rate PPN --}}
            <div class="col-span-1 sm:col-span-3 lg:col-span-5 lg:row-start-3">
                <x-label for="rate_ppn" value="{{ __('RATE PPN') }}" />
                @if ($isEdit)
                    <x-input id="rate_ppn" class="block mt-1 w-full" type="text" name="rate_ppn"
                        value="{{ old('rate_ppn', $manfeeDoc->rate_ppn ?? '') }}" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">{{ $manfeeDoc->rate_ppn ?? '-' }}</p>
                @endif
            </div>

            {{-- Jumlah --}}
            <div class="col-span-1 sm:col-span-2 lg:col-span-5 lg:col-start-1 lg:row-start-4">
                <x-label for="total" value="{{ __('Jumlah') }}" />
                @if ($isEdit)
                    <x-input id="total" class="block mt-1 w-full" type="text" name="total"
                        value="{{ old('total', $manfeeDoc->total ?? '') }}" />
                @else
                    <p class="text-gray-800 dark:text-gray-200">{{ $manfeeDoc->total ?? '-' }}</p>
                @endif
            </div>

            {{-- Nilai PPN (Disabled) --}}
            <div class="col-span-1 sm:col-span-2 lg:col-span-3 lg:col-start-6 lg:row-start-3">
                <x-label for="nilai_ppn" value="{{ __('NILAI PPN') }}" />
                @if ($isEdit)
                    <x-input id="nilai_ppn" class="block mt-1 w-full bg-gray-200 dark:bg-gray-700" type="text"
                        name="nilai_ppn" value="{{ old('nilai_ppn', $manfeeDoc->nilai_ppn ?? '') }}" readonly />
                @else
                    <p class="text-gray-800 dark:text-gray-200">{{ $manfeeDoc->nilai_ppn ?? '-' }}</p>
                @endif
            </div>
        </div>
        {{-- Akumulasi Biaya Form End --}}
    </div>
</div>
{{-- Akumulasi Biaya End --}}
