@props(['status' => '-'])

@php
    // Mapping status ke teks deskripsi
    $statuses = [
        '0' => 'Draft',
        '1' => 'Checked by Kepala Divisi',
        '2' => 'Checked by Pembendaharaan',
        '3' => 'Checked by Manager Anggaran',
        '4' => 'Checked by Direktur Keuangan',
        '5' => 'Checked by Pajak',
        '6' => 'Submit doc to Employer',
        '100' => 'Finished',
        '101' => 'Canceled',
        '102' => 'Revised',
        '103' => 'Rejected',
    ];

    // Mapping warna berdasarkan status teks
    $statusColors = [
        'Draft' => 'bg-gray-200 text-gray-800',
        'Checked by Kepala Divisi' => 'bg-blue-200 text-blue-800',
        'Checked by Pembendaharaan' => 'bg-yellow-200 text-yellow-800',
        'Checked by Manager Anggaran' => 'bg-orange-200 text-orange-800',
        'Checked by Direktur Keuangan' => 'bg-indigo-200 text-indigo-800',
        'Checked by Pajak' => 'bg-teal-200 text-teal-800',
        'Submit doc to Employer' => 'bg-cyan-200 text-cyan-800',
        'Finished' => 'bg-green-200 text-green-800',
        'Rejected' => 'bg-red-200 text-red-800',
        'Revised' => 'bg-orange-300 text-orange-900',
    ];

    // Konversi status angka menjadi teks
    $statusText = $statuses[$status] ?? '-';

    // Ambil warna yang sesuai, jika tidak ada pakai default abu-abu
    $colorClass = $statusColors[$statusText] ?? 'bg-gray-200 text-gray-800';
@endphp

<span class="px-3 py-1 text-sm font-semibold rounded-md {{ $colorClass }}">
    {{ $statusText }}
</span>
