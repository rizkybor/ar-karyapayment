<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-xl md:text-2xl text-gray-800 dark:text-gray-100 font-bold">Detail Invoice
                    #{{ $manfeeDoc->invoice_number }}</h1>
            </div>
        </div>

        {{-- Header --}}


        <x-management-fee.header :transaction_status="$transaction_status" :document_status="$document_status" :category="$category" isShowPage="true" />



        {{-- AKUMULASI BIAYA --}}
        <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
            <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-5">
                Akumulasi Biaya
            </h5>
            <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    {{-- Akun --}}
                    <div class="col-span-1">
                        <x-label for="akun" value="{{ __('Akun') }}" />
                        <p class="text-gray-800 dark:text-gray-200"></p>
                    </div>

                    {{-- DPP Pekerjaan --}}
                    <div class="col-span-1">
                        <x-label for="dpp_pekerjaan" value="{{ __('DPP Pekerjaan') }}" />
                        <p class="text-gray-800 dark:text-gray-200"></p>
                    </div>

                    {{-- RATE PPN --}}
                    <div class="col-span-1 sm:col-span-1">
                        <x-label for="rate_ppn" value="{{ __('RATE PPN') }}" />
                        <p class="text-gray-800 dark:text-gray-200"></p>
                    </div>

                    {{-- NILAI PPN --}}
                    <div class="col-span-1 sm:col-span-1">
                        <x-label for="nilai_ppn" value="{{ __('NILAI PPN') }}" />
                        <p class="text-gray-800 dark:text-gray-200"></p>
                    </div>

                    {{-- JUMLAH --}}
                    <div class="col-span-1 sm:col-span-2">
                        <x-label for="jumlah" value="{{ __('JUMLAH') }}" />
                        <p class="text-gray-800 dark:text-gray-200"></p>
                    </div>

                </div>
            </div>
        </div>

        {{-- LAMPIRAN --}}
        <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
            <div class="sm:flex sm:justify-between sm:items-center mb-5">
                <!-- Left: Title -->
                <div class=" sm:mb-0">
                    <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Lampiran</h1>
                </div>
                <!-- Right: Actions -->
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                </div>
                {{-- <x-button type="button" onclick="window.location='{{ route('management-fee.create') }}'">
                    + Data Baru
                </x-button> --}}
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <div class="p-3">
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full">
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
                            <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                                @php $i = 1; @endphp
                                {{-- @foreach ($attachments as $attachment)
                                    <tr>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center">{{ $i++ }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-left">{{ $attachment->name }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center flex items-center justify-center gap-2">
                                                <x-button-action color="purple" icon="eye"
                                                    href="{{ route('attachments.view', $attachment->id) }}">
                                                    View
                                                </x-button-action>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- DESKRIPSI --}}
        <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
            <div class="sm:flex sm:justify-between sm:items-center mb-5">
                <!-- Left: Title -->
                <div class=" sm:mb-0">
                    <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Deskripsi</h1>
                </div>
                <!-- Right: Actions -->
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                </div>
                {{-- <x-button type="button" onclick="window.location='{{ route('management-fee.create') }}'">
                    + Data Baru
                </x-button> --}}
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <div class="p-3">
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full">
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
                            <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                                @php $i = 1; @endphp
                                {{-- @foreach ($attachments as $attachment)
                                    <tr>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center">{{ $i++ }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-left">{{ $attachment->name }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center flex items-center justify-center gap-2">
                                                <x-button-action color="purple" icon="eye"
                                                    href="{{ route('attachments.view', $attachment->id) }}">
                                                    View
                                                </x-button-action>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- FAKTUR PAJAK --}}
        <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
            <div class="sm:flex sm:justify-between sm:items-center mb-5">
                <!-- Left: Title -->
                <div class=" sm:mb-0">
                    <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Faktur</h1>
                </div>
                <!-- Right: Actions -->
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                </div>
                {{-- <x-button type="button" onclick="window.location='{{ route('management-fee.create') }}'">
                    + Data Baru
                </x-button> --}}
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <div class="p-3">
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full">
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
                            <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                                @php $i = 1; @endphp
                                {{-- @foreach ($attachments as $attachment)
                                    <tr>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center">{{ $i++ }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-left">{{ $attachment->name }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center flex items-center justify-center gap-2">
                                                <x-button-action color="purple" icon="eye"
                                                    href="{{ route('attachments.view', $attachment->id) }}">
                                                    View
                                                </x-button-action>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        {{-- Tombol Kembali --}}
        <div class="form-group">
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <x-secondary-button onclick="window.location='{{ route('management-fee.index') }}'">
                    Kembali
                </x-secondary-button>
            </div>
        </div>
    </div>
</x-app-layout>
