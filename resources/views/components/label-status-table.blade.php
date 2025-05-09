@props(['status' => '-'])

@php
    // Mapping status ke teks deskripsi
    $statuses = [
        '0' => 'Draft',
        '1' => 'Checked by Kadiv',
        '2' => 'Checked by Perbendaharaan',
        '3' => 'Checked by Mgr. Anggaran',
        '4' => 'Checked by Dir. Keuangan',
        '5' => 'Checked by Pajak',
        '6' => 'Done',
        '100' => 'Finished', // belum digunakan
        '101' => 'Canceled', // belum digunakan
        '102' => 'Revised',
        '103' => 'Rejected',
    ];

    // Mapping warna berdasarkan status teks
    $statusColors = [
        'Draft' => 'bg-gray-200 text-gray-800',
        'Checked by Kadiv' => 'bg-blue-200 text-blue-800',
        'Checked by Perbendaharaan' => 'bg-yellow-200 text-yellow-800',
        'Checked by Mgr. Anggaran' => 'bg-orange-200 text-orange-800',
        'Checked by Dir. Keuangan' => 'bg-indigo-200 text-indigo-800',
        'Checked by Pajak' => 'bg-teal-200 text-teal-800',
        'Done' => 'bg-cyan-200 text-cyan-800',
        'Finished' => 'bg-green-200 text-green-800',
        'Canceled' => 'bg-gray-200 text-gray-800',
        'Rejected' => 'bg-red-200 text-red-800',
        'Revised' => 'bg-orange-300 text-orange-900',
    ];

    // Konversi status angka menjadi teks
    $statusText = $statuses[$status] ?? '-';

    // Ambil warna yang sesuai, jika tidak ada pakai default abu-abu
    $colorClass = $statusColors[$statusText] ?? 'bg-gray-200 text-gray-800';
@endphp

<span class="px-2 py-1 text-xs font-medium rounded-md {{ $colorClass }}">
    {{ $statusText }}
</span>
