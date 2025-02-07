<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">AR - Management Fee</h1>
            </div>

            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">

            </div>

            <x-button type="button" onclick="window.location='{{ route('createManagementFee') }}'">
                + Data Baru
            </x-button>


        </div>

        <!-- Cards -->
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-full xl:col-span-12 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <h2 class="font-semibold  dark:text-gray-100">Customers</h2>
                </header>
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
                                        <div class="font-semibold text-left">No Kontrak</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">Nama Pemberi Kerja</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Total Nilai Kontrak</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Jangka Waktu</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Status</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Termin Invoice</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Total</div>
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
                                        <div class="text-left">KPU-999/999</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left">###########</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center">Rp. 999.999</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center">2 Bulan</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center">###########</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center"><x-button href="">
                                                Detail Termin
                                            </x-button></div>

                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center">###########</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center gap-2">
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

        </div>

    </div>
</x-app-layout>
