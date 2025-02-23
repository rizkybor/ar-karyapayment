@props(['status' => 'Active', 'keterangan' => '', 'isEditable' => false])

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-5 gap-4">

  <!-- Box Status -->
<div class="w-full sm:w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-5">
    <div class="grid grid-cols-1 gap-4">
        {{-- Status --}}
        <div>
            <x-label for="status" value="{{ __('Status Transaksi') }}" class="text-gray-800 dark:text-gray-100" />
            @if ($isEditable)
                <select id="status" name="status" class="form-input w-full mt-1">
                    <option value="Active" {{ $status == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Non Active" {{ $status == 'Non Active' ? 'selected' : '' }}>Non Active</option>
                </select>
            @else
                <p class="mt-1 text-gray-800 dark:text-gray-200 font-semibold">{{ $status ?: 'Belum Ditentukan' }}</p>
            @endif
        </div>

        {{-- Keterangan Status --}}
        <div>
            <x-label for="keterangan_status" value="{{ __('Keterangan Status') }}" class="text-gray-800 dark:text-gray-100" />
            @if ($isEditable)
                <x-input id="keterangan_status" name="keterangan_status" type="text" class="w-full mt-1"
                    value="{{ $keterangan }}" />
            @else
                @if ($status == 'Active')
                    <p class="mt-1 text-gray-800 dark:text-gray-200">-</p>
                @elseif ($status == 'Non Active')
                    <a href="{{ route('download.keterangan') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
                        Download Keterangan
                    </a>
                @else
                    <p class="mt-1 text-gray-800 dark:text-gray-200">{{ $keterangan ?: '-' }}</p>
                @endif
            @endif
        </div>
    </div>
</div>

    <!-- Tombol Action (Sejajar dengan Card di Desktop, di Atas Card di Mobile) -->
    <div class="flex flex-wrap gap-2 sm:flex-nowrap sm:w-auto sm:items-start">
        <x-button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 text-sm">Need Info</x-button>
        <x-button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 text-sm">Reject</x-button>
        <x-button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 text-sm">Approve</x-button>
        <x-button class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 text-sm">Process</x-button>
    </div>

</div>
