<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .border-box {
            border: 1px solid black;
            padding: 5px;
        }

        .text-smaller {
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }


        .border-table th,
        .border-table td {
            border: 1px solid black;
            padding: 4px 8px;
        }

        .header {
            text-align: center;
            font-weight: bold;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .footer td {
            vertical-align: top;
        }

        .no-border {
            border: none !important;
            border-top: none;
            border-bottom: none;
        }

        .no-border-top-side {
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            border-bottom: 1px solid black;
        }
    </style>
</head>

<body class="bg-white p-8">

    @php
        $status = (int) $document->status;
        $isPerbendaharaan = auth()->user()->role === 'perbendaharaan';
        $showDraft = $status === 6 && $isPerbendaharaan;
        $isRejected = $status === 103;
        $disableWatermark = $disableWatermark ?? false;

        // Group expenses by type and sum their values
        $groupedExpenses = [];
        foreach ($detailPayments as $payment) {
            $type = $payment->expense_type ?? 'Lainnya';
            if (!isset($groupedExpenses[$type])) {
                $groupedExpenses[$type] = 0;
            }
            $groupedExpenses[$type] += $payment->nilai_biaya ?? 0;
        }

        $totalBiaya = $detailPayments->sum('nilai_biaya') ?? 0;
        $grandTotal = $totalBiaya + $accumulatedCosts->sum('nilai_manfee');
        $rowspan = 8 + count($groupedExpenses); // Update rowspan based on grouped expenses count
    @endphp

    @if (!$disableWatermark && !$showDraft)
        <div
            style="position: fixed;
           top: 35%;
           left: 12%;
           z-index: -1;
           opacity: 0.08;
           font-size: {{ $isRejected ? '100px' : '150px' }};
           transform: rotate(-30deg);
           font-weight: bold;
           color: {{ $isRejected ? '#dc2626' : '#000' }};">
            {{ $isRejected ? 'REJECTED' : 'DRAFT' }}
        </div>
    @endif

    <table width="100%" border="0" style="border-collapse: collapse;">
        <tr>
            <td style="border: none;">
                <img src="file://{{ public_path('images/logo-kpu-ls.png') }}"
                    alt="Logo KPU"style="height: 50px; width: auto;">
            </td>
        </tr>
        <tr>
            <td class="header" style="border: none;">
                <h3 style="text-decoration: underline; letter-spacing: 3px;">INVOICE</h3>
                <h3>No. {{ $document->invoice_number ?? 'Nomor surat tidak ada' }}</h3>
            </td>
        </tr>
    </table>

    <div style="min-height: 505px;">
        <!-- Penerima -->
        <table border="1" style="border-collapse: collapse; width: 50%; margin-left: 0;">
            <tr>
                <td style="border: 1px solid black; padding: 8px; text-align: left;">
                    Kepada Yth:<br>
                    <strong>{{ $contract->employee_name ?? 'NULL' }}</strong><br>
                    {{ $contract->address ?? 'NULL' }}
                </td>
            </tr>
        </table>

        <!-- Kwitansi dan Tanggal -->
        <table class="w-full mt-4 text-sm border-collapse"
            style="border: 1px solid black; width: 60%; border-collapse: collapse;">
            <tr>
                <td class="w-1/2" style="border: 1px solid black; padding: 8px;"><strong>Kwitansi</strong></td>
                <td class="w-2" style="border: 1px solid black; padding: 8px;">No.
                    {{ $document->receipt_number ?? 'NULL' }}</td>
            </tr>
            <tr>
                <td class="w-1/2" style="border: 1px solid black; padding: 8px;"><strong>Tanggal</strong></td>
                <td class="w-2" style="border: 1px solid black; padding: 8px;">
                    {{ \Carbon\Carbon::parse($document->created_at)->translatedFormat('d F Y') }}</td>
            </tr>
        </table>

        {{-- Table Detail --}}
        <table class="border-table" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th colspan="3">Keterangan</th>
                    <th colspan="2">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="{{ $rowspan }}" style="vertical-align: top;">1</td> <!-- Kolom pertama (No) -->

                    <td colspan="3" style=" border-bottom: none;">{{ $document->letter_subject ?? '-' }} -
                        {{ $document->period ?? '-' }}</td> <!-- Keterangan -->

                    <td style="border-right:none; border-bottom: none;">Rp.</td> <!-- Simbol Rupiah -->
                    <td style="text-align: right; border-left:none; border-bottom: none;">
                        {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>

                @foreach ($groupedExpenses as $type => $amount)
                    <tr>
                        <td class="no-border">{{ $type ?? '-' }}</td>
                        <td class="no-border" style="text-align: right; padding-left: 3rem">
                            Rp.</td>
                        <td
                            style="border-left: none; border-top: none; border-bottom: none; text-align: right; padding-right: 5rem">
                            {{ number_format($amount ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="no-border">&nbsp;</td>
                        <td style="border-left: none; border-top: none; border-bottom: none;">&nbsp;</td>
                    </tr>
                @endforeach

                <tr>
                    <td class="no-border">
                        Management Fee
                        {{ optional($accumulatedCosts[0] ?? null)->total_expense_manfee
                            ? rtrim(rtrim($accumulatedCosts[0]->total_expense_manfee, '0'), '.') . '%'
                            : '-' }}
                    </td>
                    <td class="no-border" style="text-align: right; padding-left: 3rem">Rp.</td>
                    <td
                        style="border-left: none; border-top: none; border-bottom: none; text-align: right; padding-right: 5rem">
                        {{ number_format($accumulatedCosts->sum('nilai_manfee') ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="no-border">&nbsp;</td>
                    <td style="border-left: none; border-top: none; border-bottom: none;">&nbsp;</td>
                </tr>

                <tr>
                    <td class="no-border"><strong>Jumlah</strong></td>
                    <td class="no-border" style="text-align: right; padding-left: 3rem; font-weight: bold;">Rp.</td>
                    <td
                        style="font-weight: bold; border-left: none; border-top:none; border-right: 1px solid black; border-bottom: none; padding: 5px; position: relative; text-align: right; padding-right: 5rem;">
                        {{ number_format($grandTotal ?? 0, 0, ',', '.') }}
                        <div style="position: absolute; top: 0; left: 5%; width: 60%; height: 1px; background: black;">
                        </div>
                    </td>
                    <td class="no-border">&nbsp;</td>
                    <td style="border-left: none; border-top: none; border-bottom: none;">&nbsp;</td>
                </tr>
                <tr>
                    <td class="no-border">
                        {{ isset($accumulatedCosts[0]) && $accumulatedCosts[0]->comment_ppn
                            ? 'PPN ' . $accumulatedCosts[0]->comment_ppn
                            : 'PPN' }}
                    </td>
                    <td class="no-border" style="text-align: right; padding-left: 3rem;">Rp.</td>
                    <td
                        style="border-left: none; border-top: none; border-bottom: none; text-align: right; padding-right: 5rem">
                        {{ number_format($accumulatedCosts->sum('nilai_ppn') ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="no-border">&nbsp;</td>
                    <td style="border-left: none; border-top: none; border-bottom: none;">
                        &nbsp;</td>
                </tr>
                <tr>
                    <td class="no-border-top-side"><strong>Jumlah Total</strong></td>
                    <td class="no-border" style="text-align: right; padding-left: 3rem; font-weight: bold;">Rp.</td>
                    <td
                        style="font-weight: bold; border-left: none; border-top:none; border-right: 1px solid black; border-bottom: 1px solid black; padding: 5px; position: relative; text-align: right; padding-right: 5rem;">
                        {{ number_format($accumulatedCosts->sum('total') ?? 0, 0, ',', '.') }}
                        <div style="position: absolute; top: 0; left: 5%; width: 60%; height: 1px; background: black;">
                        </div>
                    </td>
                    <td class="no-border-top-side">&nbsp;</td>
                    <td style="border-left: none; border-top: none;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Jumlah</strong></td>
                    <td style="border-bottom: none; border-right: none;"><strong>Rp.</strong></td>
                    <td style="text-align: right; border-left: none;">
                        <strong>{{ number_format($grandTotal ?? 0, 0, ',', '.') }}</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;">
                        {{ isset($accumulatedCosts[0]) && $accumulatedCosts[0]->comment_ppn
                            ? 'PPN ' . $accumulatedCosts[0]->comment_ppn
                            : 'PPN' }}
                    </td>
                    <td style="border-bottom: none; border-right: none;">Rp.</td>
                    <td style="text-align: right; border-left: none;">
                        {{ number_format($accumulatedCosts->sum('nilai_ppn') ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Jumlah Total</strong></td>
                    <td style="border-right: none;"><strong>Rp.</strong></td>
                    <td style="text-align: right; border-left: none;">
                        <strong>{{ number_format($accumulatedCosts->sum('total') ?? 0, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Detail Pembayaran --}}
    <table>
        <tbody>
            <tr>
                <td rowspan="6"></td>
                <td class="no-border" colspan="3">Pembayaran dapat ditransfer melalui:</td>
                <td colspan="2" style="border-bottom: none; text-align: center; vertical-align: middle;">
                    Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td class="no-border">Bank</td>
                <td class="no-border">: &nbsp;&nbsp;{{ $document->bankAccount->bank_name ?? '-' }}</td>
                <td class="no-border"></td>
                <td colspan="2" rowspan="3" style="text-align: center; border-top: none; border-bottom: none;">
                </td>
            </tr>
            <tr>
                <td class="no-border">No. Rekening</td>
                <td class="no-border">: &nbsp;&nbsp;{{ $document->bankAccount->account_number ?? '-' }}</td>
                <td class="no-border"></td>
            </tr>
            <tr>
                <td class="no-border">Cabang</td>
                <td class="no-border">: &nbsp;&nbsp;KK Jakarta Gedung PGN Pusat</td>
                <td class="no-border"></td>
            </tr>
            <tr>
                <td class="no-border">Atas Nama</td>
                <td class="no-border">: &nbsp;&nbsp;{{ $document->bankAccount->account_name ?? '-' }}</td>
                <td class="no-border"></td>
                <td colspan="2" style="border-top: none;"></td>
            </tr>
            <tr>
                <td class="no-border" style="vertical-align: top;">Email</td>
                <td class="no-border" style="vertical-align: top;">: &nbsp;&nbsp;invoice_center@pt-kpusahatama.com
                </td>
                <td class="no-border" style="vertical-align: top;"></td>
                <td colspan="2"
                    style="border-top: none; border-bottom: none; text-align: center; font-weight: normal;">
                    <br><br>
                    <strong>Sutaryo</strong>
                    <br>
                    Direktur Keuangan dan Administrasi
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer mt-2 text-smaller">
        <table border="0">
            <tr>
                <td style="width: 30%; border: none;">
                    <strong>PT. KARYA PRIMA USAHATAMA<br><em>melayani & memahami</em></strong>
                </td>
                <td style="width: 30%; border: none;">
                    RUKO KETAPANG INDAH BLOK A2 NO.8<br>Jl. K.H. Zainul Arifin<br>Jakarta Barat - 11140<br>Indonesia
                </td>
                <td style="width: 30%; border: none;">
                    <strong>T</strong>: +62 21-6343 558 <br> <strong>E</strong>: contact@pt-kpusahatama.com
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
