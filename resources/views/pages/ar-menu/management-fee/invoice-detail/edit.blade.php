<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-xl md:text-2xl text-gray-800 dark:text-gray-100 font-bold">Edit Detail Invoice
                    #{{ $manfeeDoc->invoice_number }}</h1>
            </div>
            <div class="form-group">
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                    <x-secondary-button onclick="window.location='{{ route('management-fee.index') }}'">
                        Kembali
                    </x-secondary-button>
                </div>
            </div>
        </div>
        <form action="{{ route('management-non-fee.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- for VIEW --}}
            <x-management-fee.header :transaction_status="$transaction_status" :document_status="$document_status" :category="$category" isEditable="true" />

            {{-- Detail Biaya --}}
            <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
                <div class="flex justify-between items-center mb-3">
                    <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        Detail Biaya
                    </h5>
                    <x-button-action color="violet" type="button">Tambah Detail Biaya +</x-button-action>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
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
                                        <form action="{{ route('attachments.destroy', $attachment->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus lampiran ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-delete-button type="submit">Delete</x-delete-button>
                                        </form>
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
                            <select id="akun" name="akun" class="form-input mt-1 block w-full min-h-[40px]"
                                disabled>
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
                                wire:model.live="state.test" required autocomplete="test"
                                placeholder="Percentage Rate PPN" />
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
                                wire:model.live="state.test" required autocomplete="test"
                                placeholder="= DPP * RATE PPN" disabled />
                            <x-input-error for="test" class="mt-2" />
                        </div>
                    </div>
                    {{-- Akumulasi Biaya Form End --}}
                </div>
            </div>
            {{-- Akumulasi Biaya End --}}

            {{-- LAMPIRAN --}}
            <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
                <div class="flex justify-between items-center mb-3">
                    <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        Lampiran
                    </h5>
                    <x-button-action color="violet" type="button">Tambah Lampiran +</x-button-action>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
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
                                                    <form action="{{ route('attachments.destroy', $attachment->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus lampiran ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-delete-button type="submit">Delete</x-delete-button>
                                                    </form>
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
                <div class="flex justify-between items-center mb-3">
                    <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        Deskripsi
                    </h5>
                    <x-button-action color="violet" type="button">Tambah Deskripsi +</x-button-action>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
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
                                    @php $i = 1; @endphp
                                    {{-- @foreach ($files_faktur as $file)
                                        <tr>
                                            <td class="p-2 whitespace-nowrap">
                                                <div class="text-center">{{ $i++ }}</div>
                                            </td>
                                            <td class="p-2 whitespace-nowrap">
                                                <div class="text-left">{{ $file->name }}</div>
                                            </td>
                                            <td class="p-2 whitespace-nowrap">
                                                <div class="text-center flex items-center justify-center gap-2">

                                                    <x-button-action color="purple" icon="eye"
                                                        href="{{ route('attachments.view', $file->id) }}">
                                                        View
                                                    </x-button-action>
                                                    <form action="{{ route('attachments.destroy', $file->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus lampiran ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-delete-button type="submit">Delete</x-delete-button>
                                                    </form>
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
                <div class="flex justify-between items-center mb-3">
                    <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        Faktur Pajak
                    </h5>
                    <x-button-action color="violet" type="button">Tambah Faktur Pajak +</x-button-action>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
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
                                    @php $i = 1; @endphp
                                    {{-- @foreach ($files_faktur as $file)
                                        <tr>
                                            <td class="p-2 whitespace-nowrap">
                                                <div class="text-center">{{ $i++ }}</div>
                                            </td>
                                            <td class="p-2 whitespace-nowrap">
                                                <div class="text-left">{{ $file->name }}</div>
                                            </td>
                                            <td class="p-2 whitespace-nowrap">
                                                <div class="text-center flex items-center justify-center gap-2">

                                                    <x-button-action color="purple" icon="eye"
                                                        href="{{ route('attachments.view', $file->id) }}">
                                                        View
                                                    </x-button-action>
                                                    <form action="{{ route('attachments.destroy', $file->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus lampiran ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-delete-button type="submit">Delete</x-delete-button>
                                                    </form>
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
        </form>
    </div>
    <script></script>
</x-app-layout>
