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
                 <x-label for="test" value="{{ __('Akun') }}" />
                 <select id="akun" name="akun" class="form-input mt-1 block w-full min-h-[40px]" disabled>
                     <option value="">Select Account by Accurate</option>
                 </select>
                 <x-input-error for="test" class="mt-2" />
             </div>
             {{-- Rate Manfee --}}
             <div class="col-span-1 sm:col-span-2 lg:col-span-2 lg:col-start-4">
                 <x-label for="test" value="{{ __('Rate Manfee') }}" />
                 <select id="akun" name="akun" class="form-input mt-1 block w-full min-h-[40px]">
                     <option value="">Percentage (10%)</option>
                 </select>
                 <x-input-error for="test" class="mt-2" />
             </div>

             <div class="col-span-1 sm:col-span-2 lg:col-span-3 lg:col-start-6">
                 <x-label for="test" value="{{ __('Nilai Manfee') }}" />
                 <x-input id="test" type="text" class="mt-1 block w-full min-h-[40px]"
                     wire:model.live="state.test" required autocomplete="test"
                     placeholder="= Subtotal Detail Biaya * Rate Manfee" disabled />
                 <x-input-error for="test" class="mt-2" />
             </div>

             <div class="col-span-1 sm:col-span-3 lg:col-span-5 lg:row-start-2">
                 <x-label for="test" value="{{ __('DPP') }}" />
                 <x-input id="test" type="text" class="mt-1 block w-full min-h-[40px]"
                     wire:model.live="state.test" required autocomplete="test"
                     placeholder="= Nilai Manfee (+ Jika Ada Biaya Non Personil)" disabled />
                 <x-input-error for="test" class="mt-2" />
             </div>

             <div class="col-span-1 sm:col-span-3 lg:col-span-5 lg:row-start-3">
                 <x-label for="test" value="{{ __('RATE PPN') }}" />
                 <x-input id="test" type="text" class="mt-1 block w-full min-h-[40px]"
                     wire:model.live="state.test" required autocomplete="test" placeholder="Percentage Rate PPN" />
                 <x-input-error for="test" class="mt-2" />
             </div>

             <div class="col-span-1 sm:col-span-2 lg:col-span-5 lg:col-start-1 lg:row-start-4">
                 <x-label for="test" value="{{ __('JUMLAH') }}" />
                 <x-input id="test" type="text" class="mt-1 block w-full min-h-[40px]"
                     wire:model.live="state.test" required autocomplete="test"
                     placeholder="Seluruh Jenis Biaya + Nilai Manfee + Nilai PPN" disabled />
                 <x-input-error for="test" class="mt-2" />
             </div>

             <div class="col-span-1 sm:col-span-2 lg:col-span-3 lg:col-start-6 lg:row-start-3">
                 <x-label for="test" value="{{ __('NILAI PPN') }}" />
                 <x-input id="test" type="text" class="mt-1 block w-full min-h-[40px]"
                     wire:model.live="state.test" required autocomplete="test" placeholder="= DPP * RATE PPN"
                     disabled />
                 <x-input-error for="test" class="mt-2" />
             </div>
         </div>
         {{-- Akumulasi Biaya Form End --}}
     </div>
 </div>
 {{-- Akumulasi Biaya End --}}
