<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-12">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Detail Transaksi</h1>
            </div>

            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <x-secondary-button href="{{ route('createManagementFee') }}" class="float-right">
                    Process
                </x-secondary-button>
            </div>
        </div>
        <!-- Dashboard actions End -->

        {{-- Detail Biaya --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-lg md:text-xl text-gray-800 dark:text-gray-100">Detail Biaya</h1>
            </div>

            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <x-secondary-button href="{{ route('createManagementFee') }}" class="float-right">
                    Tambah Biaya +
                </x-secondary-button>
            </div>
        </div>
        {{-- Tabel Detail Biaya --}}
        <div class="col-span-full xl:col-span-12 bg-white dark:bg-gray-800 shadow-sm rounded-xl mb-8">
            <div class="p-3">
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <!-- Table header -->
                        <thead
                            class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                            <tr>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-center">No</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Jenis Biaya</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-center">Total Jenis Biaya</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-center">Action</div>
                                </th>
                            </tr>
                        </thead>
                        <!-- Table body -->
                        <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                            <tr>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-center">1</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-left">Biaya Personil</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-center">Rp. 100.000</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-center">
                                        <x-button href="">
                                            View
                                        </x-button>
                                        <x-secondary-button href="">Edit</x-secondary-button>
                                        <x-danger-button href="">Delete</x-danger-button>
                                    </div>

                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
        {{-- Tabel Detail Biaya End --}}
        {{-- Detail Biaya End --}}

        {{-- Akumulasi Biaya --}}
        {{-- Tittle Akumulasi Biaya --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-lg md:text-xl text-gray-800 dark:text-gray-100">Akumulasi Biaya</h1>
            </div>
        </div>
        {{-- Tittle Akumulasi Biaya End --}}
        <div class="mt-5 md:mt-0 md:col-span-2 mb-8">
            <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">

                {{-- Akumulasi Biaya Form --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                    <div class="col-span-1 sm:col-span-2 lg:col-span-3">
                        <x-label for="test" value="{{ __('Akun') }}" />
                        <x-input id="test" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.test" required autocomplete="test" />
                        <x-input-error for="test" class="mt-2" />
                    </div>

                    <div class="col-span-1 sm:col-span-2 lg:col-span-2 lg:col-start-4">
                        <x-label for="test" value="{{ __('Rate Manfee') }}" />
                        <x-input id="test" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.test" required autocomplete="test" />
                        <x-input-error for="test" class="mt-2" />
                    </div>

                    <div class="col-span-1 sm:col-span-2 lg:col-span-3 lg:col-start-6">
                        <x-label for="test" value="{{ __('Nilai Manfee') }}" />
                        <x-input id="test" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.test" required autocomplete="test" />
                        <x-input-error for="test" class="mt-2" />
                    </div>

                    <div class="col-span-1 sm:col-span-3 lg:col-span-5 lg:row-start-2">
                        <x-label for="test" value="{{ __('DPP') }}" />
                        <x-input id="test" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.test" required autocomplete="test" />
                        <x-input-error for="test" class="mt-2" />
                    </div>

                    <div class="col-span-1 sm:col-span-3 lg:col-span-5 lg:row-start-3">
                        <x-label for="test" value="{{ __('RATE PPN') }}" />
                        <x-input id="test" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.test" required autocomplete="test" />
                        <x-input-error for="test" class="mt-2" />
                    </div>

                    <div class="col-span-1 sm:col-span-2 lg:col-span-5 lg:col-start-1 lg:row-start-4">
                        <x-label for="test" value="{{ __('NILAI PPN') }}" />
                        <x-input id="test" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.test" required autocomplete="test" />
                        <x-input-error for="test" class="mt-2" />
                    </div>

                    <div class="col-span-1 sm:col-span-2 lg:col-span-3 lg:col-start-6 lg:row-start-3">
                        <x-label for="test" value="{{ __('JUMLAH') }}" />
                        <x-input id="test" type="text" class="mt-1 block w-full min-h-[40px]"
                            wire:model.live="state.test" required autocomplete="test" />
                        <x-input-error for="test" class="mt-2" />
                    </div>
                </div>
                {{-- Akumulasi Biaya Form End --}}
            </div>
        </div>
        {{-- Akumulasi Biaya End --}}
        {{-- Lampiran --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-lg md:text-xl text-gray-800 dark:text-gray-100">Lampiran</h1>
            </div>

            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <x-secondary-button href="{{ route('createManagementFee') }}" class="float-right">
                    Tambah Lampiran +
                </x-secondary-button>
            </div>
        </div>
        {{-- Tabel Lampiran --}}
        <div class="col-span-full xl:col-span-12 bg-white dark:bg-gray-800 shadow-sm rounded-xl mb-8">
            <div class="p-3">
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <!-- Table header -->
                        <thead
                            class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                            <tr>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-center">No</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Nama File</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-center">Action</div>
                                </th>
                            </tr>
                        </thead>
                        <!-- Table body -->
                        <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                            <tr>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-center">1</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-left">Biaya Personil</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-center">
                                        <x-button href="">
                                            View
                                        </x-button>
                                        <x-danger-button href="">Delete</x-danger-button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
        {{-- Tabel Lampiran End --}}
        {{-- Lampiran End --}}
        {{-- Deskripsi --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-lg md:text-xl text-gray-800 dark:text-gray-100">Deskripsi</h1>
            </div>

            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <x-secondary-button href="{{ route('createManagementFee') }}" class="float-right">
                    Tambah Deskripsi +
                </x-secondary-button>
            </div>
        </div>
        {{-- Tabel Deskripsi --}}
        <div class="col-span-full xl:col-span-12 bg-white dark:bg-gray-800 shadow-sm rounded-xl mb-8">
            <div class="p-3">
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <!-- Table header -->
                        <thead
                            class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                            <tr>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-center">No</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Catatan</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-center">Action</div>
                                </th>
                            </tr>
                        </thead>
                        <!-- Table body -->
                        <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                            <tr>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-center">1</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-left">Biaya Personil</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-center">
                                        <x-button href="">
                                            View
                                        </x-button>
                                        <x-danger-button href="">Delete</x-danger-button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
        {{-- Tabel Deskripsi End --}}
        {{-- Deskripsi End --}}
        {{-- Pajak --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-lg md:text-xl text-gray-800 dark:text-gray-100">Pajak</h1>
            </div>
            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <x-secondary-button href="{{ route('createManagementFee') }}" class="float-right">
                    Tambah Pajak +
                </x-secondary-button>
            </div>
        </div>
        {{-- Tabel Pajak --}}
        <div class="col-span-full xl:col-span-12 bg-white dark:bg-gray-800 shadow-sm rounded-xl mb-8">
            <div class="p-3">
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <!-- Table header -->
                        <thead
                            class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                            <tr>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-center">No</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Nama File</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-center">Action</div>
                                </th>
                            </tr>
                        </thead>
                        <!-- Table body -->
                        <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                            <tr>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-center">1</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-left">Biaya Personil</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-center">
                                        <x-button href="">
                                            View
                                        </x-button>
                                        <x-danger-button href="">Delete</x-danger-button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
        {{-- Tabel Pajak End --}}
        {{-- Pajak End --}}
    </div>
</x-app-layout>
