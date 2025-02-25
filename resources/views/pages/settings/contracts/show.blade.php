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
                        <div><strong>Nomor Kontrak:</strong> {{ $contract->contract_number }}</div>
                        <div><strong>Nama Perusahaan:</strong> {{ $contract->employee_name }}</div>
                        <div><strong>Nilai Kontrak:</strong> Rp {{ number_format($contract->value, 0, ',', '.') }}</div>
                        <div><strong>Tanggal Mulai:</strong>
                            {{ \Carbon\Carbon::parse($contract->start_date)->translatedFormat('l, d F Y') }}</div>
                        <div><strong>Tanggal Selesai:</strong>
                            {{ \Carbon\Carbon::parse($contract->end_date)->translatedFormat('l, d F Y') }}</div>
                        <div><strong>Tipe Kontrak:</strong> {{ ucwords(str_replace('_', ' ', $contract->type)) }}</div>
                        <div><strong>Path Contract:</strong> <a href="{{ asset($contract->path) }}" class="text-blue-500"
                                target="_blank">Lihat Dokumen</a></div>
                        @if ($mstBillType->isNotEmpty())
                            <div><strong>Tipe Pembayaran:</strong>
                                {{ $mstBillType->pluck('bill_type')->implode(', ') }}
                            </div>
                        @endif
                        <div><strong>Alamat:</strong> {{ $contract->address }}</div>
                        <div><strong>Unit Kerja:</strong> {{ ucwords(str_replace('_', ' ', $contract->work_unit)) }}
                        </div>
                    </div>
                </div>

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
