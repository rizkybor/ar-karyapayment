@props(['status' => '-'])

@php
    // Mapping status ke teks deskripsi
    $statuses = [
        '0' => 'Draft',
        '1' => 'Checked by Kepala Divisi',
        '2' => 'Checked by Pembendaharaan',
        '3' => 'Checked by Manager Keuangan',
        '4' => 'Checked by Direktur Keuangan',
        '5' => 'Checked by Pajak',
        '6' => 'Close Doc at Pembendaharaan',
        '9' => 'Requires Information',
        '99' => 'Rejected',
        '100' => 'Completed',
    ];

    // Mapping warna berdasarkan status teks
    $statusColors = [
        'Draft' => 'bg-purple-200 text-purple-800',
        'Checked by Kepala Divisi' => 'bg-blue-200 text-blue-800',
        'Checked by Pembendaharaan' => 'bg-yellow-200 text-yellow-800',
        'Checked by Manager Keuangan' => 'bg-orange-200 text-orange-800',
        'Checked by Direktur Keuangan' => 'bg-indigo-200 text-indigo-800',
        'Checked by Pajak' => 'bg-teal-200 text-teal-800',
        'Requires Information' => 'bg-pink-200 text-pink-800',
        'Rejected' => 'bg-red-200 text-red-800',
        'Completed' => 'bg-green-200 text-green-800',
    ];

    // Konversi status angka menjadi teks
    $statusText = $statuses[$status] ?? '-';

    // Ambil warna yang sesuai, jika tidak ada pakai default abu-abu
    $colorClass = $statusColors[$statusText] ?? 'bg-gray-200 text-gray-800';
@endphp

<span class="px-3 py-1 text-sm font-semibold rounded-md {{ $colorClass }}">
    {{ $statusText }}
</span>
