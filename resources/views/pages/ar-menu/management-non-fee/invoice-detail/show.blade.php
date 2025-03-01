<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Detail Invoice
                    #{{ $nonManfeeDocument['invoice_number'] }}
                </h1>
            </div>
            {{-- Tombol Kembali --}}
            <div class="form-group">
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                    <x-secondary-button onclick="window.location='{{ route('management-non-fee.index') }}'">
                        Kembali
                    </x-secondary-button>
                </div>
            </div>
        </div>

        <div class="border border-white-300 dark:border-white-700 my-6"></div>

        {{-- HEADER --}}
        <x-management-non-fee.header :transaction_status="$nonManfeeDocument['is_active']" :document_status="$nonManfeeDocument['status']" :document="$nonManfeeDocument" isShowPage="true" />
        
        {{-- AKUMULASI BIAYA --}}
        <x-management-non-fee.accumulated-costs.index :nonManfeeDocument="$nonManfeeDocument" :isEdit="false" />

        {{-- LAMPIRAN --}}
        <x-management-non-fee.attachments.index :nonManfeeDocument="$nonManfeeDocument" />

        {{-- DESKRIPSI --}}
        <x-management-non-fee.descriptions.index :nonManfeeDocument="$nonManfeeDocument" />

        {{-- FAKTUR PAJAK --}}
        <x-management-non-fee.tax-files.index :nonManfeeDocument="$nonManfeeDocument" />
    </div>
</x-app-layout>
