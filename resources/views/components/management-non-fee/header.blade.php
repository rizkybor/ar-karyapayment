@props(['transaction_status' => 'Active', 'document_status' => '', 'isEditable' => false])

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-5 gap-4">

  <!-- Box Status -->
<div class="w-full sm:w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-5">
    <div class="grid grid-cols-1 gap-4">
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
                {{ $transaction_status === 'True' ? 'Aktif' : ($transaction_status === 'False' ? 'Tidak Aktif' : 'Belum Ditentukan') }}
            </p>
        </div>

        {{-- Status Document --}}
        <div>
            {{-- <x-label for="document_status" value="{{ __('Status Dokumen') }}" class="text-gray-800 dark:text-gray-100" />
            @if ($isEditable)
                <x-input id="document_status" name="document_status" type="text" class="w-full mt-1"
                    value="{{ $document_status }}" />
            @else
                @if ($transaction_status == 'Active')
                    <p class="mt-1 text-gray-800 dark:text-gray-200">-</p>
                @elseif ($transaction_status == 'Non Active')
                    <a href="{{ route('download.keterangan') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
                        Download Keterangan
                    </a>
                @else
                    <p class="mt-1 text-gray-800 dark:text-gray-200">{{ $document_status ?: '-' }}</p>
                @endif
            @endif --}}
            <x-label for="document_status" value="{{ __('Status Dokumen') }}" class="text-gray-800 dark:text-gray-100" />
            <x-label-status :status="$document_status" />
        </div>
    </div>
</div>

    <!-- Tombol Action (Sejajar dengan Card di Desktop, di Atas Card di Mobile) -->
    <div class="flex flex-wrap gap-2 sm:flex-nowrap sm:w-auto sm:items-start">
        <x-button-action color="blue" icon="print">Print</x-button-action>
        <x-button-action color="teal" icon="paid">
            Paid
        </x-button-action>
        <x-button-action color="yellow" icon="cancel">Batal Transaksi</x-button-action>
        <x-button-action color="orange" icon="info">Need Info</x-button-action>
        <x-button-action color="red" icon="reject">Reject</x-button-action>
        <x-button-action color="green" icon="approve">Approve</x-button-action>
        <x-button-action color="green" icon="process">Process</x-button-action>
    </div>

</div>
