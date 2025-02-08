<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Tambah Data Baru</h1>
            </div>

            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <x-secondary-button onclick="window.location='{{ route('dashboard') }}'" class="float-right">
                    Batal
                </x-secondary-button>

                <x-secondary-button onclick="window.location='{{ route('detailManagementFee') }}'" class="float-right">
                    Simpan
                </x-secondary-button>
            </div>
        </div>
        <!-- Dashboard actions end -->

        {{-- Tambah Data Baru --}}
        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <x-label for="test" value="{{ __('Milih Kontrak') }}" />
                        <x-input id="test" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.test" required autocomplete="test" />
                        <x-input-error for="test" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="name" value="{{ __('No Surat') }}" />
                        <x-input id="name" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.name" required autocomplete="name" />
                        <x-input-error for="name" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="name" value="{{ __('No Invoice') }}" />
                        <x-input id="name" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.name" required autocomplete="name" />
                        <x-input-error for="name" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="name" value="{{ __('Periode / Termin') }}" />
                        <x-input id="name" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.name" required autocomplete="name" />
                        <x-input-error for="name" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="name" value="{{ __('No Kwitansi') }}" />
                        <x-input id="name" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.name" required autocomplete="name" />
                        <x-input-error for="name" class="mt-2" />
                    </div>
                    <div class="sm:row-span-2">
                        <x-label for="perihal" value="{{ __('Perihal Surat') }}" />
                        <x-input-wide id="perihal" name="perihal" type="text"
                            class="mt-1 block w-full min-h-[40px]" />
                        <x-input-error for="name" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="name" value="{{ __('Nama Pemberi Kerja') }}" />
                        <x-input id="name" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.name" required autocomplete="name" />
                        <x-input-error for="name" class="mt-2" />
                    </div>
                    <div class="sm:row-start-5">
                        <x-label for="name" value="{{ __('Type') }}" />
                        <x-input id="name" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.name" required autocomplete="name" />
                        <x-input-error for="name" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>
        {{-- Tambah Data Baru End --}}
    </div>
</x-app-layout>
