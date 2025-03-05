<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-xl md:text-2xl text-gray-800 dark:text-gray-100 font-bold">Detail Invoice
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

        {{-- HEADER --}}
        <x-management-fee.header :transaction_status="$manfeeDoc['is_active']" :document_status="$manfeeDoc['status']" :latestApprover=$latestApprover :document="$manfeeDoc"
            isShowPage="true" />

        {{-- DETAIL BIAYA --}}
        <x-management-fee.detail-biaya.index :manfeeDoc="$manfeeDoc" :jenis_biaya="$jenis_biaya" :isEdit="false" />

        {{-- AKUMULASI BIAYA --}}
        <x-management-fee.accumulated-costs.index :manfeeDoc="$manfeeDoc" :isEdit="false" :subtotals="$subtotals"
            :subtotalBiayaNonPersonil="$subtotalBiayaNonPersonil" />

        {{-- LAMPIRAN --}}
        <x-management-fee.attachments.index :manfeeDoc="$manfeeDoc" />

        {{-- DESKRIPSI --}}
        <x-management-fee.descriptions.index :manfeeDoc="$manfeeDoc" />

        {{-- FAKTUR PAJAK --}}
        <x-management-fee.tax-files.index :manfeeDoc="$manfeeDoc" />


    </div>
</x-app-layout>
<x-management-fee.histories :manfeeDoc="$manfeeDoc" />
