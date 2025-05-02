<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            {{-- Judul --}}
            <div class="md:col-span-1 flex justify-between">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detail Kontrak</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Informasi lengkap tentang kontrak, termasuk detail pihak terkait, durasi, dan persyaratan
                        khusus.
                    </p>
                </div>
            </div>
            {{-- Judul End --}}

            {{-- Detail Kontrak --}}
            <div class="mt-5 md:mt-0 md:col-span-2">
                <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="grid grid-cols-1 gap-y-6">

                        {{-- Nomor Kontrak --}}
                        <div><strong>Nomor Kontrak:</strong> {{ $contract->contract_number }}</div>

                        {{-- Nama Perusahaan --}}
                        <div><strong>Nama Perusahaan:</strong> {{ $contract->employee_name }}</div>

                        {{-- Judul Perusahaan --}}
                        <div><strong>Judul Kontrak:</strong> {{ $contract->title }}</div>

                        {{-- Kategori Kontrak --}}
                        <div><strong>Kategori Kontrak:</strong> {{ $contract->category }}</div>

                        {{-- Nilai Kontrak --}}
                        <div><strong>Nilai Kontrak:</strong> Rp {{ number_format($contract->value, 0, ',', '.') }}</div>

                        {{-- Tanggal Kontrak --}}
                        <div><strong>Tanggal Kontrak:</strong>
                            {{ \Carbon\Carbon::parse($contract->contract_date)->translatedFormat('l, d F Y') }}</div>

                        {{-- Tanggal Mulai --}}
                        <div><strong>Tanggal Mulai:</strong>
                            {{ \Carbon\Carbon::parse($contract->start_date)->translatedFormat('l, d F Y') }}</div>

                        {{-- Tanggal Selesai --}}
                        <div><strong>Tanggal Selesai:</strong>
                            {{ \Carbon\Carbon::parse($contract->end_date)->translatedFormat('l, d F Y') }}</div>

                        {{-- Tipe Kontrak --}}
                        <div><strong>Tipe Kontrak:</strong> {{ ucwords(str_replace('_', ' ', $contract->type)) }}</div>

                        {{-- Path Contract --}}
                        <div><strong>Path Contract:</strong> <a href="{{ asset($contract->path) }}" class="text-blue-500"
                                target="_blank">Lihat Dokumen</a></div>

                        {{-- Tipe Pembayaran --}}
                        @if ($mstBillType->isNotEmpty())
                            <div><strong>Tipe Pembayaran:</strong>
                                {{ $mstBillType->pluck('bill_type')->implode(', ') }}
                            </div>
                        @endif

                        {{-- Alamat --}}
                        <div><strong>Alamat:</strong> {{ $contract->address }}</div>

                        {{-- Unit Kerja --}}
                        <div><strong>Unit Kerja:</strong> {{ ucwords(str_replace('_', ' ', $contract->work_unit)) }}
                        </div>

                        {{-- Tabel Invoice --}}
                        @if ($manfeeDocuments->isNotEmpty() || $nonManfeeDocuments->isNotEmpty())
                            <strong>Invoice yang dibuat:</strong>
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead
                                        class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                                        <tr>
                                            <th class="p-2 whitespace-nowrap">
                                                <div class="font-semibold text-center">No</div>
                                            </th>
                                            <th class="p-2 whitespace-nowrap">
                                                <div class="font-semibold text-center">Invoice Number</div>
                                            </th>
                                            <th class="p-2 whitespace-nowrap">
                                                <div class="font-semibold text-center">Receipt Number</div>
                                            </th>
                                            <th class="p-2 whitespace-nowrap">
                                                <div class="font-semibold text-center">Letter Number</div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-400">
                                        @php $i = 1; @endphp
                                        @foreach (array_merge($manfeeDocuments->toArray(), $nonManfeeDocuments->toArray()) as $index => $doc)
                                            <tr>
                                                <td class="p-2 whitespace-nowrap">
                                                    <div class="text-center">{{ $i++ }}</div>
                                                </td>
                                                <td class="p-2 whitespace-nowrap">
                                                    <div class="text-center">
                                                        {{ $doc['invoice_number'] ?? 'Tidak ada invoice' }}</div>
                                                </td>
                                                <td class="p-2 whitespace-nowrap"">
                                                    <div class="text-center">
                                                        {{ $doc['receipt_number'] ?? 'Tidak ada receipt' }}</div>
                                                </td>
                                                <td class="p-2 whitespace-nowrap">
                                                    <div class="text-center">
                                                        {{ $doc['letter_number'] ?? 'Tidak ada letter number' }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Button Kembali --}}
                <div
                    class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-gray-700/20 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                    <x-secondary-button
                        onclick="window.location='{{ route('contracts.index') }}'">Kembali</x-secondary-button>
                </div>
            </div>
            {{-- Detail Kontrak End --}}
        </div>
    </div>
</x-app-layout>
