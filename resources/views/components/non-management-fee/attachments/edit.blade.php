@props(['nonManfeeDocument'])

<div class="mt-5 mb-5 md:mt-0 md:col-span-2">
    <div class="flex justify-between items-center mb-3">
        <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
            Lampiran
        </h5>
        <x-modal.non-management-fee.modal-create-attachment :nonManfeeDocument="$nonManfeeDocument" />
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
        <div class="p-3">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead
                        class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                        <tr>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">No</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Nama File</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-end">Aksi</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @php $i = 1; @endphp
                        @if (!empty($nonManfeeDocument->attachments) && $nonManfeeDocument->attachments->count())
                            @foreach ($nonManfeeDocument->attachments as $file)
                                <tr x-data="{ modalOpen: false }">
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left">{{ $i++ }}</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left">{{ $file->file_name }}</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center flex items-center justify-end gap-2">
                                            <x-button-action color="violet" icon="eye" @click="modalOpen = true">
                                                View
                                            </x-button-action>

                                            <!-- Panggil Komponen Modal -->
                                            <x-modal.global.modal-view-global-file :file="$file"
                                                :nonManfeeDocument="$nonManfeeDocument" />
                                            <!-- End Komponen Modal -->

                                            <x-button-action color="red" icon="trash"
                                                onclick="confirm('Apakah Anda yakin ingin menghapus lampiran ini?') 
                                                && document.getElementById('delete-attachment-{{ $file->id }}').submit()">
                                                Hapus
                                            </x-button-action>
                                            <form id="delete-attachment-{{ $file->id }}" method="POST"
                                                action="{{ route('non-management-fee.attachments.destroy', ['id' => $nonManfeeDocument->id, 'attachment_id' => $file->id]) }}"
                                                class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="text-start p-4 text-gray-500">
                                    Belum memiliki lampiran.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
