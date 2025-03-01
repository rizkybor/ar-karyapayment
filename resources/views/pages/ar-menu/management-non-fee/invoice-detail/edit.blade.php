<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Edit Detail Invoice
                    #{{ $nonManfeeDocument['invoice_number'] }}
                </h1>
            </div>
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
        <x-management-non-fee.header :transaction_status="$nonManfeeDocument['is_active']" :document_status="$nonManfeeDocument['status']" isEditable="true" />

        <div class="grid grid-cols-1 gap-6 mt-6">
            {{-- AKUMULASI BIAYA --}}
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                <x-management-non-fee.accumulated-costs.edit :nonManfeeDocument="$nonManfeeDocument" :akunOptions="$akunOptions" :isEdit="false" />
            </div>

            {{-- LAMPIRAN --}}
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                <x-management-non-fee.attachments.edit :nonManfeeDocument="$nonManfeeDocument" />
            </div>

            {{-- DESKRIPSI --}}
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                <x-management-non-fee.descriptions.edit :nonManfeeDocument="$nonManfeeDocument" />
            </div>

            {{-- FAKTUR PAJAK --}}
            @role('pajak')
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-management-non-fee.tax-files.edit :nonManfeeDocument="$nonManfeeDocument" />
                </div>
            @endrole
        </div>

    </div>
</x-app-layout>
