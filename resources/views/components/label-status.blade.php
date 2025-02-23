@props(['status' => '-'])

@php
    // Mapping warna berdasarkan status
    $statusColors = [
        'Draft' => 'bg-purple-200 text-purple-800',
        'On Progress' => 'bg-yellow-200 text-yellow-800',
        'Paid' => 'bg-green-200 text-green-800',
    ];

    // Jika tidak ada, gunakan default abu-abu
    $colorClass = $statusColors[$status] ?? 'bg-gray-200 text-gray-800';
@endphp

<span class="px-3 py-1 text-sm font-semibold rounded-md {{ $colorClass }}">
    {{ $status ?: '-' }}
</span>