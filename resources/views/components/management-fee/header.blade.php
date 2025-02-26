@props([
    'transaction_status' => '',
    'document_status' => '',
    'isEditable' => false,
    'isShowPage' => false,
    'category' => '',
    'document' => [],
])

<div class="flex flex-col lg:flex-row justify-between items-start gap-4 mb-5">

    <!-- Box Status -->
    <div class="w-full lg:w-1/2 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Status Transaksi --}}
            <div>
                <x-label for="transaction_status" value="{{ __('Status Transaksi') }}"
                    class="text-gray-800 dark:text-gray-100" />
                <p class="mt-1 text-gray-800 dark:text-gray-200 font-semibold">
                    {{ $transaction_status == 'Active' ? 'Aktif' : 'Tidak Aktif' }}
                </p>
            </div>

            {{-- Status Dokumen --}}
            <div>
                <x-label for="document_status" value="{{ __('Status Dokumen') }}"
                    class="text-gray-800 dark:text-gray-100" />
                <x-label-status :status="$document_status" />
            </div>
        </div>

        <div class="mt-4">
            {{-- Jenis --}}
            <x-label for="transaction_status" value="{{ __('Jenis') }}" class="text-gray-800 dark:text-gray-100" />
            <p class="mt-1 text-gray-800 dark:text-gray-200 font-semibold">
                {{ ucwords(str_replace('_', ' ', $category)) }}
            </p>
        </div>
    </div>

    <!-- Tombol Action -->
    @if ($isShowPage)
        <div class="flex flex-wrap gap-2 sm:flex-nowrap sm:w-auto sm:items-start">
            <x-button-action color="blue" icon="print">Print</x-button-action>
            <x-button-action color="teal" icon="paid">
                Paid
            </x-button-action>
            <x-button-action color="yellow" icon="cancel">Batal Transaksi</x-button-action>
            <x-button-action color="orange" icon="info">Need Info</x-button-action>
            <x-button-action color="red" icon="reject">Reject</x-button-action>
            <x-button-action color="green" icon="approve">Approve</x-button-action>
            <form action="{{ route('management-fee.processApproval', $document['id']) }}" method="POST"
                onsubmit="return confirm('Apakah Anda yakin ingin memproses dokumen ini?');">
                @csrf
                @method('PUT')
                <x-button-action color="green" icon="process" type="submit">Process</x-button-action>
            </form>
        </div>
    @endif

</div>
