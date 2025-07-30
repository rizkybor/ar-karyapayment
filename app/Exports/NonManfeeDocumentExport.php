<?php

namespace App\Exports;

use App\Models\NonManfeeDocument;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class NonManfeeDocumentExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $ids;

    public function __construct(
        $ids
    ) {
        $this->ids = explode(',', $ids);
    }

    public function collection()
    {
        return NonManfeeDocument::with(['contract', 'creator', 'accumulatedCosts', 'taxFiles'])
            ->whereIn('id', $this->ids)
            ->get()
            ->map(function ($doc, $index) {
                $fakturPajak = $doc->taxFiles[0]->file_name ?? '-';
                $tglTagihan = $doc->created_at;
                $tglJatuhTempo = Carbon::parse($doc->expired_at);
                $hariIni = Carbon::now();

                $umurPiutang = $hariIni->diffInDays($tglTagihan);
                $selisihJatuhTempo = $hariIni->diffInDays($tglJatuhTempo, false);

                // Pastikan accumulatedCosts tidak null
                $dpp = optional($doc->accumulatedCosts)->dpp ?? '';
                $nilaiPpn = optional($doc->accumulatedCosts)->nilai_ppn ?? '';
                $totalTagihan = optional($doc->accumulatedCosts)->total ?? 0;

                $nilaiPokok = $doc->detailPayments
                    ->whereIn('expense_type', ['Biaya Personil', 'biaya_personil'])
                    ->sum('nilai_biaya');

                $nilaiNonPersonil = $doc->detailPayments
                    ->whereIn('expense_type', ['Biaya Non Personil', 'biaya_non_personil'])
                    ->sum('nilai_biaya');

                $nilaiLainLain = $doc->detailPayments
                    ->whereNotIn('expense_type', [
                        'Biaya Non Personil',
                        'biaya_non_personil',
                        'Biaya Personil',
                        'biaya_personil'
                    ])
                    ->sum('nilai_biaya');

                $keterangan = '-';
                if ($doc->status == 103) {
                    $keterangan = 'Rejected';
                } else {
                    if ($doc->status_payment == 'Lunas') {
                        $keterangan = $doc->status_payment;
                    } else {
                        $keterangan = 'Outstanding';
                    }
                }

                $transaksi = '-';
                if ($doc->letter_subject && $doc->period) {
                    $transaksi = $doc->letter_subject . ' - ' . $doc->period;
                } elseif ($doc->letter_subject) {
                    $transaksi = $doc->letter_subject;
                }

                return [
                    'No' => $index + 1,
                    'No. Tagihan' => $doc->invoice_number ?? '-',
                    'No. Perjanjian / Kontrak' => optional($doc->contract)->contract_number ?? '-',
                    'Tgl Perjanjian' => $doc->contract && $doc->contract->start_date
                        ? Carbon::parse($doc->contract->start_date)->format('d M Y')
                        : '-',
                    'Pemberi Kerja' => optional($doc->contract)->employee_name ?? '-',
                    'Kode' => optional($doc->contract)->contract_initial ?? '-',
                    'PIC KPU' => optional($doc->creator)->name ?? '-',
                    'PIC PEMBERI KERJA' => '-',
                    'Jenis Tagihan' => $doc->category ?? '',
                    'Tgl. Tagihan' => $tglTagihan?->format('d M Y') ?? '-',
                    'Umur Piutang' => $umurPiutang . 'd',
                    'Tanggal Bayar ke TAD' => '-',
                    'Tgl. Jatuh Tempo' => $tglJatuhTempo?->format('d M Y') ?? '-',
                    'Jatuh Tempo' => ($selisihJatuhTempo >= 0)
                        ? $selisihJatuhTempo . ' hari lagi'
                        : 'Lewat ' . abs($selisihJatuhTempo) . ' hari',
                    'Tgl. Dokumen Diterima & Penerima' => '-',
                    'No. Faktur Pajak' => $fakturPajak ?? '-',
                    'Transaksi' => $transaksi,
                    'Nilai Pokok' => $nilaiPokok ?? '-',
                    'Non Personil' =>  $nilaiNonPersonil ?? '-',
                    'Lain-lain' => $nilaiLainLain ?? '-',
                    'Manfee' => '-',
                    'DPP' => $dpp ?? '-',
                    'PPN' => $nilaiPpn ?? '-',
                    'Total Tagihan' => round($totalTagihan) ?? '-', // Nilai Total Invoice
                    'Outstanding' => round($totalTagihan) ?? '-', // Nilai Total Tagihan
                    'Tgl Terima' => '-',
                    'Nilai Diterima' => '-',
                    'PPh (ps. 23, 4(2), 22) & WAPU' => '-',
                    'Keterangan' => $keterangan ?? '-',
                    'Update status tagihan' => '-',
                    'No. PERMENT PGNMAS / Notes' => '-'
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Tagihan',
            'No. Perjanjian / Kontrak',
            'Tgl Perjanjian',
            'Pemberi Kerja',
            'Kode',
            'PIC KPU',
            'PIC PEMBERI KERJA',
            'Jenis Tagihan',
            'Tgl. Tagihan',
            'Umur Piutang',
            'Tanggal Bayar ke TAD',
            'Tgl. Jatuh Tempo',
            'Jatuh Tempo',
            'Tgl. Dokumen Diterima & Penerima',
            'No. Faktur Pajak',
            'Transaksi',
            'Nilai Pokok',
            'Non Personil',
            'Lain-lain',
            'Manfee',
            'DPP',
            'PPN',
            'Total Tagihan',
            'Outstanding',
            'Tgl Terima',
            'Nilai Diterima',
            'PPh (ps. 23, 4(2), 22) & WAPU',
            'Keterangan',
            'Update status tagihan',
            'No. PERMENT PGNMAS / Notes'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'DAECF9'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 35,
            'C' => 25,
            'D' => 15,
            'E' => 25,
            'F' => 10,
            'G' => 20,
            'H' => 50,
            'I' => 25,
            'J' => 15,
            'K' => 15,
            'L' => 50,
            'M' => 15,
            'N' => 15,
            'O' => 40,
            'P' => 30,
            'Q' => 70,
            'R' => 15,
            'S' => 15,
            'T' => 15,
            'U' => 15,
            'V' => 15,
            'W' => 15,
            'X' => 15,
            'Y' => 15,
            'Z' => 15,
            'AA' => 15,
            'AB' => 40,
            'AC' => 15,
            'AD' => 40,
            'AE' => 40,
        ];
    }

    // Fungsi pembantu untuk menghasilkan kolom dari A sampai AE
    public function excelColumnsRange($start = 'A', $end = 'AE')
    {
        $columns = [];
        $col = $start;

        while (true) {
            $columns[] = $col;
            if ($col === $end) {
                break;
            }
            $col++;
        }

        return $columns;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set tinggi baris header
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Ambil kolom dari A sampai AD
                $columns = $this->excelColumnsRange('A', 'AE');

                // Wrap text & vertical alignment
                foreach ($columns as $col) {
                    $sheet->getStyle("{$col}")->getAlignment()->setWrapText(true);
                    $sheet->getStyle("{$col}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }

                // Freeze header baris pertama
                $sheet->freezePane('A2');

                // Set tinggi semua baris yang ada datanya menjadi 30
                $highestRow = $sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(30);
                }
            },
        ];
    }
}
