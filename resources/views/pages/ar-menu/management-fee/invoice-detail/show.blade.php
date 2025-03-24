<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Detail Invoice
                    #{{ $manfeeDoc->invoice_number }}</h1>
            </div>
            {{-- Tombol Kembali --}}
            <div class="form-group">
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                    <x-secondary-button onclick="openHistoryModal({{ $manfeeDoc->id }})">
                        Riwayat Dokumen
                    </x-secondary-button>
                    <x-secondary-button onclick="window.location='{{ route('management-fee.index') }}'">
                        Kembali
                    </x-secondary-button>
                </div>
            </div>
        </div>

        <div class="border border-white-300 dark:border-white-700 my-6"></div>

        {{-- HEADER --}}
        <x-management-fee.header :transaction_status="$manfeeDoc['is_active']" :document_status="$manfeeDoc['status']" :latestApprover=$latestApprover :document="$manfeeDoc"
            isShowPage="true" />

        {{-- Get error message --}}
        @if (session()->has('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
                🚨 {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 mt-6">

            {{-- DETAIL BIAYA --}}
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                <x-management-fee.detail-biaya.index :manfeeDoc="$manfeeDoc" :jenis_biaya="$jenis_biaya" :isEdit="false"
                    :account_detailbiaya="$account_detailbiaya" />
            </div>

            {{-- AKUMULASI BIAYA --}}
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                <x-management-fee.accumulated-costs.index :manfeeDoc="$manfeeDoc" :isEdit="false" :subtotals="$subtotals"
                    :subtotalBiayaNonPersonil="$subtotalBiayaNonPersonil" />
            </div>

            {{-- LAMPIRAN --}}
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                <x-management-fee.attachments.index :manfeeDoc="$manfeeDoc" />
            </div>

            {{-- DESKRIPSI --}}
            {{-- <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                <x-management-fee.descriptions.index :manfeeDoc="$manfeeDoc" />
            </div> --}}

            {{-- FAKTUR PAJAK --}}
            @if (!empty($manfeeDoc->taxFiles))
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-management-fee.tax-files.index :manfeeDoc="$manfeeDoc" />
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
<x-management-fee.histories :manfeeDoc="$manfeeDoc" />
