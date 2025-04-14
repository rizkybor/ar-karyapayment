<?php

namespace App\Exports;

use App\Models\NonManfeeDocument;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NonManfeeDocumentExport implements FromCollection, WithHeadings
{
    protected $ids;

    public function __construct($ids)
    {
        $this->ids = explode(',', $ids);
    }

    public function collection()
    {
        return NonManfeeDocument::whereIn('id', $this->ids)
            ->with('contract') // Mengambil relasi contract
            ->get()
            ->map(function ($doc) {
                return [
                    'ID' => $doc->id,
                    'No Kontrak' => $doc->contract->contract_number ?? '-',
                    'Nama Pemberi Kerja' => $doc->contract->employee_name ?? '-',
                    'Total Nilai Kontrak' => $doc->contract->value ?? '-',
                    'Jangka Waktu' => $doc->period,
                    'Status' => $doc->status,
                    'Created At' => $doc->created_at->format('d-m-Y H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return ['ID', 'No Kontrak', 'Nama Pemberi Kerja', 'Total Nilai Kontrak', 'Jangka Waktu', 'Status', 'Created At'];
    }
}