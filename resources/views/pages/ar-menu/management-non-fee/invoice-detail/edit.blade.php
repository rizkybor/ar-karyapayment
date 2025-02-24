<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Edit Detail Invoice #{{ $document['id'] }}</h1>
            </div>
            <div class="form-group">
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                    <x-secondary-button onclick="window.location='{{ route('management-non-fee.index') }}'">
                        Batal
                    </x-secondary-button>
                    <x-button-action color="violet" type="submit">Simpan Detail Invoice</x-button-action>
                </div>
            </div>
        </div>
        <form action="{{ route('management-non-fee.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
                <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 sm:grid-cols-1 gap-6">
                        <div>
                            <x-label for="type" value="{{ __('Status') }}" />
                        </div>

                        <div>
                            <x-label for="keterangan_status" value="{{ __('Keterangan Status') }}" />

                        </div>

                    </div>
                </div>
            </div> --}}
            {{-- for VIEW --}}
            {{-- <x-management-non-fee.header :status="$document['status']" :keterangan="$document['letter_subject']" /> --}}
            <x-management-non-fee.header :transaction_status="$document['is_active']" :document_status="$document['status']" isEditable="true" />

            {{-- AKUMULASI BIAYA --}}
            <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
                <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">
                    Akumulasi Biaya
                </h5>
                <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                        {{-- Akun --}}
                        <div class="col-span-1">
                            <x-label for="akun" value="{{ __('Akun') }}" />
                            <select id="akun" name="akun" class="form-input w-full mt-1">
                                <option value="">Select Account by Accurate</option>
                            </select>
                        </div>

                        {{-- DPP Pekerjaan --}}
                        <div class="col-span-1">
                            <x-label for="dpp_pekerjaan" value="{{ __('DPP Pekerjaan') }}" />
                            <x-input id="dpp_pekerjaan" name="dpp_pekerjaan" type="text" class="w-full mt-1"
                                placeholder="Free Text" />
                        </div>

                        {{-- RATE PPN --}}
                        <div class="col-span-1 sm:col-span-1">
                            <x-label for="rate_ppn" value="{{ __('RATE PPN') }}" />
                            <x-input id="rate_ppn" name="rate_ppn" type="text" class="w-full mt-1"
                                placeholder="Free Text (Percentage)" />
                        </div>

                        {{-- NILAI PPN --}}
                        <div class="col-span-1 sm:col-span-1">
                            <x-label for="nilai_ppn" value="{{ __('NILAI PPN') }}" />
                            <x-input id="nilai_ppn" name="nilai_ppn" type="text" class="w-full mt-1"
                                placeholder="= DPP * RATE PPN" disabled />
                        </div>

                        {{-- JUMLAH --}}
                        <div class="col-span-1 sm:col-span-2">
                            <x-label for="jumlah" value="{{ __('JUMLAH') }}" />
                            <x-input id="jumlah" name="jumlah" type="text" class="w-full mt-1"
                                placeholder="= DPP Pekerjaan + Nilai PPN" disabled />
                        </div>

                    </div>
                </div>
            </div>

            {{-- LAMPIRAN --}}
            <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
                <div class="flex justify-between items-center mb-3">
                    <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        Lampiran
                    </h5>
                    <x-button-action color="violet" type="button" class="border border-violet-500 text-violet-500 bg-violet-300 hover:bg-violet-500 hover:text-white px-4 py-2 rounded-lg transition duration-200">
                        Tambah Lampiran +
                    </x-button-action>
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
                                            <div class="font-semibold text-right">Action</div>
                                        </th>
                                    </tr>
                                </thead>
                                <!-- Table body -->
                                <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                                    @php $i = 1; @endphp
                                    @foreach ($attachments as $attachment)
                                        <tr>
                                            <td class="p-2 whitespace-nowrap">
                                                <div class="text-center">{{ $i++ }}</div>
                                            </td>
                                            <td class="p-2 whitespace-nowrap">
                                                <div class="text-left">{{ $attachment->name }}</div>
                                            </td>
                                            <td class="p-2 whitespace-nowrap text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <x-button-action color="yellow" icon="pencil" href="{{ route('attachments.edit', $attachment->id) }}">
                                                        Edit
                                                    </x-button-action>
                                                    <form action="{{ route('attachments.destroy', $attachment->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus lampiran ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-delete-button type="submit">Hapus</x-delete-button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DESKRIPSI --}}
            <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
                <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">
                    Despkripsi
                </h5>
                <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
                    <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="grid grid-cols-1 sm:grid-cols-1 gap-6">
                            <div>
                                <x-input id="dpp_pekerjaan" name="dpp_pekerjaan" type="text" class="w-full mt-1"
                                placeholder="Free Text" />
                            </div>
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
                    <x-button-action color="violet" type="button" class="border border-violet-500 text-violet-500 bg-violet-300 hover:bg-violet-500 hover:text-white px-4 py-2 rounded-lg transition duration-200">
                        Tambah Faktur Pajak +
                    </x-button-action>
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
                                            <div class="font-semibold text-right">Action</div>
                                        </th>
                                    </tr>
                                </thead>
                                <!-- Table body -->
                                <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                                    @php $i = 1; @endphp
                                    @foreach ($files_faktur as $file)
                                        <tr>
                                            <td class="p-2 whitespace-nowrap">
                                                <div class="text-center">{{ $i++ }}</div>
                                            </td>
                                            <td class="p-2 whitespace-nowrap">
                                                <div class="text-left">{{ $file->name }}</div>
                                            </td>
                                            <td class="p-2 whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-2">
                                                    <x-button-action color="yellow" icon="pencil" href="{{ route('attachments.edit', $file->id) }}">
                                                        Edit
                                                    </x-button-action>
                                                    <form action="{{ route('attachments.destroy', $file->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus lampiran ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-delete-button type="submit">Hapus</x-delete-button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
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
