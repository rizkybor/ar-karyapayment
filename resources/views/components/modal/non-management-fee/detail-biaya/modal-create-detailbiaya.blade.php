@props(['nonManfeeDocument', 'jenis_biaya', 'account_detailbiaya'])


<div x-data="{
    modalOpen: false,
    selectedExpenseType: '',
    filterTable() {
        const rows = document.querySelectorAll('#detail-biaya-table tbody tr');
        console.log('Selected Expense Type:', this.selectedExpenseType); // Debug selected value
        rows.forEach(row => {
            const expenseType = row.querySelector('td:nth-child(4)').textContent.trim();
            console.log('Row Expense Type:', expenseType); // Debug row value
            if (this.selectedExpenseType === '' || expenseType === this.selectedExpenseType) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    },
    changeSelectedExpenseType() {
        this.filterTable();
    }
}">
    <div class="flex justify-between items-center mb-3">
        <x-button-action class="px-4 py-2 text-white rounded-md" color="violet" @click="modalOpen = true">
            + Detail Biaya
        </x-button-action>
    </div>

    <!-- Modal -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 flex items-center justify-center" x-show="modalOpen" x-cloak>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-5xl w-full"
            @click.outside="modalOpen = false">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Edit Detail Biaya</h3>

            <!-- Jenis Biaya -->
            <div class="mb-4">
                <label for="expense_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis
                    Biaya</label>
                <select id="expense_type" name="expense_type"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring focus:ring-blue-500"
                    x-model="selectedExpenseType" @change="changeSelectedExpenseType">
                    <option value="">Semua Jenis Biaya</option>
                    @foreach ($jenis_biaya as $jenis_biayas)
                        <option value="{{ $jenis_biayas }}">{{ $jenis_biayas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end items-center mb-3">
                <x-modal.non-management-fee.detail-biaya.modal-create-data-detailbiaya :nonManfeeDocument="$nonManfeeDocument" :jenis_biaya="$jenis_biaya"
                    :account_detailbiaya="$account_detailbiaya" />
            </div>

            <h3 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3"
                x-text="selectedExpenseType ? `List Detail ${selectedExpenseType}` : 'List Detail Semua Biaya'"></h3>

            <!-- Tabel Detail Biaya -->
            <div class="overflow-x-auto">
                <table id="detail-biaya-table" class="table-auto w-full">
                    <thead
                        class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                        <tr>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-center">No</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-center">Account</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-center">Uraian</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-center">Jenis Biaya</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-center">Nilai Biaya</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-center">Aksi</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @php $i = 1; @endphp
                        @if (!empty($nonManfeeDocument) && $nonManfeeDocument->detailPayments && $nonManfeeDocument->detailPayments->count())
                            @foreach ($nonManfeeDocument->detailPayments as $docdetails)
                                <tr>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center">{{ $i++ }}</div>
                                    </td>

                                    @php
                                        // Cari akun berdasarkan nomor account yang ada di setiap $docdetails->account
                                        $selectedAkun = collect($account_detailbiaya)->firstWhere(
                                            'no',
                                            (string) $docdetails->account,
                                        );
                                    @endphp

                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center">
                                            @if ($selectedAkun)
                                                ({{ $selectedAkun['no'] }})
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center">{{ $docdetails->account_name }}</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center">
                                            {{ $docdetails->expense_type }}
                                        </div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center">Rp
                                            {{ number_format($docdetails->nilai_biaya, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center flex items-center justify-center gap-2">
                                            <!-- Button View -->
                                            {{-- <x-button-action color="violet" icon="eye"
                                                href="{{ route('management-fee.detail_payments.show', ['id' => $nonManfeeDocument->id, 'detail_payment_id' => $docdetails->id]) }}">
                                                View
                                            </x-button-action> --}}
                                            <x-modal.management-fee.detail-biaya.modal-edit-data-detailbiaya
                                                :nonManfeeDocument="$nonManfeeDocument" :jenis_biaya="$jenis_biaya" :account_detailbiaya="$account_detailbiaya"
                                                :detailPaymentId="$docdetails" />

                                            <!-- Button Hapus -->
                                            <x-button-action color="red" icon="trash"
                                                onclick="confirm('Apakah Anda yakin ingin menghapus detail biaya ini?') 
                                            && document.getElementById('delete-attachment-{{ $docdetails->id }}').submit()">
                                                Hapus
                                            </x-button-action>

                                            <form id="delete-attachment-{{ $docdetails->id }}" method="POST"
                                                action="{{ route('management-non-fee.detail_payments.destroy', ['id' => $docdetails->document_id, 'detail_payment_id' => $docdetails->id]) }}"
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
                                <td colspan="6" class="text-center p-4 text-gray-500">
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
