@props(['manfeeDoc'])

<div class="mt-5 mb-5 md:mt-0 md:col-span-2">
    <div class="flex justify-between items-center mb-3">
        <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">
            Deskripsi
        </h5>
        <x-modal.management-fee.modal-create-descriptions :manfeeDoc="$manfeeDoc" />
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
                                <div class="font-semibold text-center">Deskripsi</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-center">Aksi</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @php $i = 1; @endphp
                        @if (!empty($manfeeDoc->descriptions) && $manfeeDoc->descriptions->count())
                            @foreach ($manfeeDoc->descriptions as $desc)
                                <tr>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center">{{ $i++ }}</div>
                                    </td>
                                    <td class="p-2 text-left break-words max-w-xs">
                                        {{ $desc->description }}
                                    </td>

                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center flex items-center justify-center gap-2">
                                            <form id="delete-description-{{ $desc->id }}" method="POST"
                                                action="{{ route('management-fee.descriptions.destroy', ['id' => $manfeeDoc->id, 'description_id' => $desc->id]) }}"
                                                class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            <x-button-action color="red" icon="trash"
                                                onclick="confirm('Apakah Anda yakin ingin menghapus deskripsi ini?') 
                                                && document.getElementById('delete-description-{{ $desc->id }}').submit()">
                                                Hapus
                                            </x-button-action>

                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="text-center p-4 text-gray-500">
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
