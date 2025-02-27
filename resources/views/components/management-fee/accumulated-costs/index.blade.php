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
                 <x-label for="akun" value="{{ __('Akun') }}" />
                 @if ($isEdit)
                     <x-input id="akun" class="block mt-1 w-full" type="text" name="akun"
                         value="{{ old('akun', $ManfeeDocument->akun ?? '') }}" />
                 @else
                     <p class="text-gray-800 dark:text-gray-200">{{ $ManfeeDocument->akun ?? '-' }}</p>
                 @endif
             </div>
             {{-- Rate Manfee --}}
             <div class="col-span-1 sm:col-span-2 lg:col-span-2 lg:col-start-4">
                 <x-label for="rate_manfee" value="{{ __('Rate Manfee') }}" />
                 @if ($isEdit)
                     <x-input id="rate_manfee" class="block mt-1 w-full" type="text" name="rate_manfee"
                         value="{{ old('rate_manfee', $ManfeeDocument->rate_manfee ?? '') }}" />
                 @else
                     <p class="text-gray-800 dark:text-gray-200">{{ $ManfeeDocument->rate_manfee ?? '-' }}</p>
                 @endif
             </div>

             <div class="col-span-1 sm:col-span-2 lg:col-span-3 lg:col-start-6">
                 <x-label for="nilai_manfee" value="{{ __('Nilai Manfee') }}" />
                 @if ($isEdit)
                     <x-input id="nilai_manfee" class="block mt-1 w-full" type="text" name="nilai_manfee"
                         value="{{ old('nilai_manfee', $ManfeeDocument->nilai_manfee ?? '') }}" />
                 @else
                     <p class="text-gray-800 dark:text-gray-200">{{ $ManfeeDocument->nilai_manfee ?? '-' }}</p>
                 @endif
             </div>

             <div class="col-span-1 sm:col-span-3 lg:col-span-5 lg:row-start-2">
                 <x-label for="dpp_pekerjaan" value="{{ __('DPP Pekerjaan') }}" />
                 @if ($isEdit)
                     <x-input id="dpp_pekerjaan" class="block mt-1 w-full" type="text" name="dpp_pekerjaan"
                         value="{{ old('dpp_pekerjaan', $ManfeeDocument->dpp_pekerjaan ?? '') }}" />
                 @else
                     <p class="text-gray-800 dark:text-gray-200">{{ $ManfeeDocument->dpp_pekerjaan ?? '-' }}</p>
                 @endif
             </div>

             <div class="col-span-1 sm:col-span-3 lg:col-span-5 lg:row-start-3">
                 <x-label for="rate_ppn" value="{{ __('RATE PPN') }}" />
                 @if ($isEdit)
                     <x-input id="rate_ppn" class="block mt-1 w-full" type="text" name="rate_ppn"
                         value="{{ old('rate_ppn', $ManfeeDocument->rate_ppn ?? '') }}" />
                 @else
                     <p class="text-gray-800 dark:text-gray-200">{{ $ManfeeDocument->rate_ppn ?? '-' }}</p>
                 @endif
             </div>

             <div class="col-span-1 sm:col-span-2 lg:col-span-5 lg:col-start-1 lg:row-start-4">
                 <x-label for="jumlah" value="{{ __('JUMLAH') }}" />
                 @if ($isEdit)
                     <x-input id="jumlah" class="block mt-1 w-full" type="text" name="jumlah"
                         value="{{ old('jumlah', $ManfeeDocument->jumlah ?? '') }}" />
                 @else
                     <p class="text-gray-800 dark:text-gray-200">{{ $ManfeeDocument->jumlah ?? '-' }}</p>
                 @endif
             </div>

             <div class="col-span-1 sm:col-span-2 lg:col-span-3 lg:col-start-6 lg:row-start-3">
                 <x-label for="nilai_ppn" value="{{ __('NILAI PPN') }}" />
                 @if ($isEdit)
                     <x-input id="nilai_ppn" class="block mt-1 w-full" type="text" name="nilai_ppn"
                         value="{{ old('nilai_ppn', $ManfeeDocument->nilai_ppn ?? '') }}" />
                 @else
                     <p class="text-gray-800 dark:text-gray-200">{{ $ManfeeDocument->nilai_ppn ?? '-' }}</p>
                 @endif
             </div>
         </div>
         {{-- Akumulasi Biaya Form End --}}
     </div>
 </div>
 {{-- Akumulasi Biaya End --}}
