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
        <x-management-fee.header :transaction_status="$manfeeDoc['is_active']" :document="$manfeeDoc" :document_status="$manfeeDoc['status']" isEditable="true" />

        <div class="grid grid-cols-1 gap-6 mt-6">
            @if ($manfeeDoc->status == 0 || $manfeeDoc->status == 102)
                {{-- DETAIL BIAYA --}}
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-management-fee.detail-biaya.edit :manfeeDoc="$manfeeDoc" :jenis_biaya="$jenis_biaya" :account_dummy="$account_dummy" />
                </div>

                {{-- AKUMULASI BIAYA --}}
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-management-fee.accumulated-costs.edit :manfeeDoc="$manfeeDoc" :subtotals="$subtotals" :subtotalBiayaNonPersonil="$subtotalBiayaNonPersonil"
                        :rate_manfee="$rate_manfee" :account_dummy="$account_dummy" :isEdit="false" />
                </div>

                {{-- LAMPIRAN --}}
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-management-fee.attachments.edit :manfeeDoc="$manfeeDoc" />
                </div>

                {{-- DESKRIPSI --}}
                {{-- <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-management-fee.descriptions.edit :manfeeDoc="$manfeeDoc" />
                </div> --}}
            @endif

            {{-- FAKTUR PAJAK --}}
            @role('pajak')
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-management-fee.tax-files.edit :manfeeDoc="$manfeeDoc" />
                </div>
            @endrole
        </div>
    </div>
    <script></script>
</x-app-layout>
