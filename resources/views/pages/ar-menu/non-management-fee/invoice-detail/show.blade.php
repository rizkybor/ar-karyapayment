<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <x-label class="text-sm mt-1">Dibuat oleh : {{ $nonManfeeDocument->creator->name ?? '-' }} ({{ $nonManfeeDocument->creator->department ?? '-' }}) </x-label>
                <h1 class="text-2xl md:text-2xl text-gray-800 dark:text-gray-100 font-bold">Detail Invoice
                    : {{ $nonManfeeDocument['invoice_number'] }}
                </h1>
            </div>
            {{-- Tombol Kembali --}}
            <div class="form-group">
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                    <x-secondary-button onclick="openHistoryModal({{ $nonManfeeDocument->id }})">
                        Riwayat Dokumen
                    </x-secondary-button>
                    <x-secondary-button onclick="window.location='{{ route('non-management-fee.index') }}'">
                        Kembali
                    </x-secondary-button>
                </div>
            </div>
        </div>

        <div class="border border-white-300 dark:border-white-700 my-6"></div>

        {{-- HEADER --}}
        <x-non-management-fee.header :transaction_status="$nonManfeeDocument['is_active']" :document_status="$nonManfeeDocument['status']" :latestApprover=$latestApprover
            :document="$nonManfeeDocument" isShowPage="true" />

        {{-- Get error message --}}
        @if (session()->has('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
                🚨 {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 mt-6">
            {{-- DETAIL BIAYA --}}
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                <x-non-management-fee.detail-biaya.index :nonManfeeDocument="$nonManfeeDocument" :jenis_biaya="$jenis_biaya" :isEdit="false"
                    :account_detailbiaya="$account_detailbiaya" />
            </div>

            {{-- AKUMULASI BIAYA --}}
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                <x-non-management-fee.accumulated-costs.index :nonManfeeDocument="$nonManfeeDocument" :optionAccount="$optionAccount" :isEdit="false" />
            </div>

            {{-- LAMPIRAN --}}
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                <x-non-management-fee.attachments.index :nonManfeeDocument="$nonManfeeDocument" />
            </div>

            {{-- DESKRIPSI --}}
            {{-- <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                <x-non-management-fee.descriptions.index :nonManfeeDocument="$nonManfeeDocument" />
            </div> --}}

            {{-- FAKTUR PAJAK --}}
            {{-- @role('pajak') --}}
            @if (!empty($nonManfeeDocument->taxFiles))
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-non-management-fee.tax-files.index :nonManfeeDocument="$nonManfeeDocument" />
                </div>
            @endif

            {{-- @endrole --}}
        </div>
    </div>

</x-app-layout>
<x-non-management-fee.histories :nonManfeeDocument=$nonManfeeDocument />
