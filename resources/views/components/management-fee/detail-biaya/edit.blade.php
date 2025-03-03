@props(['manfeeDoc'])

<div class="mt-5 mb-5 md:mt-0 md:col-span-2">
    <div class="flex justify-between items-center mb-3">
        <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
            Tambah Detail Biaya
        </h5>
        <x-modal.management-fee.modal-create-detailbiaya :manfeeDoc="$manfeeDoc" />
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
                                <div class="font-semibold text-left">Jenis Biaya</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Total Jenis Biaya</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-center">Aksi</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @php
                            $i = 1;
                            $groupedDetails = $manfeeDoc->detailPayments
                                ->groupBy('expense_type')
                                ->map(function ($group) {
                                    return [
                                        'total' => $group->sum('nilai_biaya'),
                                        'details' => $group,
                                    ];
                                });
                        @endphp

                        @if (!empty($manfeeDoc) && $manfeeDoc->detailPayments && $manfeeDoc->detailPayments->count())
                            @foreach ($groupedDetails as $expenseType => $group)
                                <tr>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center">{{ $i++ }}</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left">
                                            {{ ucwords(str_replace('_', ' ', $expenseType)) }}
                                        </div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left">Rp.
                                            {{ number_format($group['total'], 0, ',', '.') }}</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center flex items-center justify-center gap-2">
                                            <!-- Button View -->
                                            <x-button-action color="violet" icon="eye"
                                                href="{{ route('management-fee.detail_payments.show', ['id' => $manfeeDoc->id, 'detail_payment_id' => $group['details']->first()->id]) }}">
                                                View
                                            </x-button-action>

                                            <!-- Button Hapus -->
                                            <x-button-action color="red" icon="trash"
                                                onclick="confirm('Apakah Anda yakin ingin menghapus detail biaya ini?') 
                                            && document.getElementById('delete-attachment-{{ $group['details']->first()->id }}').submit()">
                                                Hapus
                                            </x-button-action>

                                            <form id="delete-attachment-{{ $group['details']->first()->id }}"
                                                method="POST"
                                                action="{{ route('management-fee.detail_payments.destroy', ['id' => $group['details']->first()->document_id, 'detail_payment_id' => $group['details']->first()->id]) }}"
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
                                <td colspan="4" class="text-center p-4 text-gray-500">
                                    Belum memiliki Detail Biaya.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
