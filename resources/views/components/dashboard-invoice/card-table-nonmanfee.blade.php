<div class="col-span-full xl:col-span-6 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
    <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
        <h2 class="font-semibold dark:text-gray-100">Invoice (Billing) - Non Management Fee</h2>
    </header>
    <div class="p-3">

        <!-- Table dengan Scrolling -->
        <div class="overflow-y-auto max-h-64 scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600">
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
                            <div class="font-semibold text-center">Status</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                            <div class="font-semibold text-center">Total</div>
                        </th>
                    </tr>
                </thead>
                <!-- Table body -->
                <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                    @php $i = 1; @endphp
                    @forelse($dataInvoices as $invoice)
                        <tr class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                            onclick="confirmRedirect('/invoice/{{ $invoice->id }}')">
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-center">{{ $i++ }}</div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-left">{{ $invoice->contract_number ?? '-' }}</div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-left">{{ $invoice->employer_name ?? '-' }}</div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-center">
                                    <x-label-status-table :status="$invoice->status">
                                        Detail Termin
                                    </x-label-status-table>
                                </div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-center">Rp. {{ number_format($invoice->total, 0, ',', '.') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">Tidak ada data tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    function confirmRedirect(url) {
        if (confirm("Apakah Anda yakin ingin melihat detail invoice ini?")) {
            window.location.href = url;
        }
    }
</script>
