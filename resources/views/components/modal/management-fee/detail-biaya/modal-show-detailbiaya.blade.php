@props(['manfeeDoc', 'jenis_biaya'])

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
    <!-- Tombol Show hanya muncul jika ada data -->
    @if (!empty($manfeeDoc) && $manfeeDoc->detailPayments && $manfeeDoc->detailPayments->count())
        <div class="flex justify-between items-center mb-3">
            <x-button-action class="px-4 py-2 text-white rounded-md" color="violet" icon="eye"
                @click="modalOpen = true">
                Show
            </x-button-action>
        </div>
    @endif

    <!-- Modal -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 flex items-center justify-center" x-show="modalOpen" x-cloak>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-3xl w-full"
            @click.outside="modalOpen = false">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informasi Detail Biaya</h3>

            <!-- Jenis Biaya -->
            <div class="mb-4">
                <label for="expense_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis
                    Biaya</label>
                <select id="expense_type" name="expense_type"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring focus:ring-blue-500"
                    x-model="selectedExpenseType" @change="changeSelectedExpenseType">
                    <option value="">Pilih Jenis Biaya</option>
                    @foreach ($jenis_biaya as $jenis_biayas)
                        <option value="{{ $jenis_biayas }}">{{ $jenis_biayas }}
                        </option>
                    @endforeach
                </select>
            </div>

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
                                <div class="font-semibold text-left">Account</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Uraian</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Jenis Biaya</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Nilai Biaya</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @php $i = 1; @endphp
                        @if (!empty($manfeeDoc) && $manfeeDoc->detailPayments && $manfeeDoc->detailPayments->count())
                            @foreach ($manfeeDoc->detailPayments as $docdetails)
                                <tr>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-center">{{ $i++ }}</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left">{{ $docdetails->account }}</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left">{{ $docdetails->uraian }}</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left">
                                            {{ $docdetails->expense_type }}
                                        </div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left">Rp
                                            {{ number_format($docdetails->nilai_biaya, 0, ',', '.') }}</div>
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
