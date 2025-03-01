<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-xl md:text-2xl text-gray-800 dark:text-gray-100 font-bold">Edit Detail Invoice
                    #{{ $manfeeDoc->invoice_number }}</h1>
            </div>
            <div class="form-group">
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                    <x-secondary-button onclick="window.location='{{ route('management-fee.index') }}'">
                        Kembali
                    </x-secondary-button>
                </div>
            </div>
        </div>

        {{-- HEADER --}}
        <x-management-fee.header :transaction_status="$transaction_status" :document_status="$document_status" :category="$category" isEditable="true" />

        {{-- DETAIL BIAYA --}}
        <x-management-fee.detail-biaya.edit :manfeeDoc="$manfeeDoc" />

        {{-- AKUMULASI BIAYA --}}
        {{-- <x-management-fee.accumulated-costs.edit :manfeeDoc="$manfeeDoc" :isEdit="false" /> --}}

        {{-- LAMPIRAN --}}
        <x-management-fee.attachments.edit :manfeeDoc="$manfeeDoc" />

        {{-- DESKRIPSI --}}
        <x-management-fee.descriptions.edit :manfeeDoc="$manfeeDoc" />

        {{-- FAKTUR PAJAK --}}
        <x-management-fee.tax-files.edit :manfeeDoc="$manfeeDoc" />

    </div>
    <script></script>
</x-app-layout>
