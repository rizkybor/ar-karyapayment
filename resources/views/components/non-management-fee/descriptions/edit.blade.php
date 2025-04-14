<div class="mt-5 mb-5 md:mt-0 md:col-span-2">
    <div class="flex justify-between items-center mb-3">
        <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
            Deskripsi
        </h5>
        <x-modal.non-management-fee.modal-create-description :nonManfeeDocument="$nonManfeeDocument" />
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
                                <div class="font-semibold text-left">Deskripsi</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-end">Aksi</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @php $i = 1; @endphp
                        @if (!empty($nonManfeeDocument->descriptions) && $nonManfeeDocument->descriptions->count())
                            @foreach ($nonManfeeDocument->descriptions as $desc)
                                <tr>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left">{{ $i++ }}</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left">{{ $desc->description }}</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center flex items-center justify-end gap-2">
                                            <x-button-action color="red" icon="trash"
                                                onclick="confirm('Apakah Anda yakin ingin menghapus deskripsi ini?') 
                                                && document.getElementById('delete-description-{{ $desc->id }}').submit()">
                                                Hapus
                                            </x-button-action>
                                            <form id="delete-description-{{ $desc->id }}" method="POST"
                                                action="{{ route('non-management-fee.descriptions.destroy', ['id' => $nonManfeeDocument->id, 'description_id' => $desc->id]) }}"
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
                                    Belum memiliki deskripsi.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
