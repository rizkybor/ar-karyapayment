@props([
    'transaction_status' => '', 
    'document_status' => '', 
    'isEditable' => false, 
    'isShowPage' => false,
    'document' => []
])

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-5 gap-4">

  <!-- Box Status -->
<div class="w-full sm:w-1/2 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-5">
    <div class="grid grid-cols-2 gap-4">
        {{-- Status Transaksi --}}
        <div>
            {{-- <x-label for="transaction_status" value="{{ __('Status Transaksi') }}" class="text-gray-800 dark:text-gray-100" />
            @if ($isEditable)
                <select id="transaction_status" name="transaction_status" class="form-input w-full mt-1">
                    <option value="Active" {{ $transaction_status == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Non Active" {{ $transaction_status == 'Non Active' ? 'selected' : '' }}>Non Active</option>
                </select>
            @else
                <p class="mt-1 text-gray-800 dark:text-gray-200 font-semibold">{{ $transaction_status ?: 'Belum Ditentukan' }}</p>
            @endif --}}
            <x-label for="transaction_status" value="{{ __('Status Transaksi') }}" class="text-gray-800 dark:text-gray-100" />
            <p class="mt-1 text-gray-800 dark:text-gray-200 font-semibold">
                {{ $transaction_status ? 'Invoice Aktif' : 'Invoice Tidak Aktif' }}
            </p>
        </div>

        {{-- Status Document --}}
        <div>
            <x-label for="document_status" value="{{ __('Status Dokumen') }}" class="text-gray-800 dark:text-gray-100" />
            <x-label-status :status="$document_status" />
        </div>
    </div>

    <br/>

    <div class="grid grid-cols-1 gap-4">
        {{-- Jenis --}}
        <div>
            <x-label for="transaction_status" value="{{ __('Jenis') }}" class="text-gray-800 dark:text-gray-100" />
            <p class="mt-1 text-gray-800 dark:text-gray-200 font-semibold">
                Management Non Fee
            </p>
        </div>
    </div>
</div>

    <!-- Tombol Action (Sejajar dengan Card di Desktop, di Atas Card di Mobile) -->
    @if ($isShowPage)
    <div class="flex flex-wrap gap-2 sm:flex-nowrap sm:w-auto sm:items-start">
        @if (auth()->user()->role !== 'maker')
            <x-button-action color="blue" icon="print">Print</x-button-action>
            <x-button-action color="teal" icon="paid">Paid</x-button-action>
            <x-button-action color="yellow" icon="cancel">Batal Transaksi</x-button-action>
            <x-button-action color="orange" icon="info">Need Info</x-button-action>
            <x-button-action color="red" icon="reject">Reject</x-button-action>
            <x-button-action color="green" icon="approve">Approve</x-button-action>
        @endif

        @if (auth()->user()->role === 'maker')
            {{-- Proccess --}}
            <form action="{{ route('management-non-fee.processApproval', $document['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memproses dokumen ini?');">
                @csrf
                @method('PUT')
                <x-button-action color="orange" icon="reply" type="submit">Reply Info</x-button-action>
            </form>

            {{-- Reply Info --}}
            <form action="{{ route('management-non-fee.processApproval', $document['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memproses dokumen ini?');">
                @csrf
                @method('PUT')
                <x-button-action color="green" icon="process" type="submit">Process</x-button-action>
            </form>
        @endif
    </div>
    @endif


</div>
