<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .signature-container {
            position: absolute;
            bottom: 91px;
            width: 100%;
        }

        .border-box {
            border: 1px solid black;
            padding: 5px;
        }
    </style>
</head>

<body class="p-8">
    @php
        $status = (int) $document->status;
        $isPerbendaharaan = auth()->user()->role === 'perbendaharaan';
        $showDraft = $status === 6 && $isPerbendaharaan;
        $isRejected = $status === 103;
        $disableWatermark = $disableWatermark ?? false;

        $groupedExpenses = [];
        foreach ($detailPayments as $payment) {
            $type = $payment->expense_type ?? 'Lainnya';
            if (!isset($groupedExpenses[$type])) {
                $groupedExpenses[$type] = 0;
            }
            $groupedExpenses[$type] += $payment->nilai_biaya ?? 0;
        }

        $grandTotal = $detailPayments->sum('nilai_biaya') ?? 0;
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

    <div class="bg-white p-8 border-box"">
        <!-- Header -->
        <div class="flex justify-between items-start border-b pb-2" style="margin-bottom: 20px;">
            <table width="100%" border="0" style="border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: left; padding-left: 5px; padding-top: 5px;">
                            <img src="file://{{ public_path('images/logo-kpu-ls.png') }}" alt="Logo KPU"
                                style="height: 50px; width: auto; display: block; margin-left: 10px; margin-top: 10px;">
                        </th>
                    </tr>
                    <tr>
                        <td class="header" style="border: none; text-align: center;">
                            <h3 style="text-decoration: underline; letter-spacing: 3px;">KWITANSI</h3>
                            <h3>No. {{ $document->receipt_number ?? 'Nomor surat tidak ada' }}</h3>
                        </td>
                    </tr>
                </thead>
            </table>

            <table border="1" style="border-collapse: collapse; width: 50%; margin-left: 0; margin-top: 24px;">
                <tr>
                    <td style="border: 1px solid black; padding: 8px; text-align: left;">
                        Sudah Terima Dari :<br>
                        <strong>{{ $contract->employee_name ?? 'NULL' }}</strong><br>
                        {{ $contract->address ?? 'NULL' }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Terbilang -->
        <div style="margin-bottom: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <td
                            style="border: 1px solid black; padding: 10px; text-align: center; width: 30%; line-height: 1; vertical-align: middle; height: 40px;">
                            Terbilang :
                        </td>
                        <td
                            style="border: 1px solid black; padding: 10px; text-align: left; font-style: italic; font-weight: bold; width: 70%; line-height: 1; vertical-align: middle; height: 40px;">
                            {{ $terbilang ?? 'Tidak ada nilai' }} Rupiah
                        </td>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- Detail Pembayaran -->
        <div class="border-box" style="margin-bottom: 20px;">
            <p class="font-semibold" style="height: 5px; margin: 2px 0; line-height: 1;">Untuk Pembayaran :</p>
            <div style="padding: 10px;">
                <div class="border-box">
                    <p class="italic">{{ $document->letter_subject ?? '-' }} - {{ $document->period ?? '-' }}</p>
                </div>

                <div style="min-height: 230px;">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 10px;">
                        @foreach ($groupedExpenses as $type => $amount)
                            <tr>
                                <td class="no-border">{{ $type ?? '-' }}</td>
                                <td class="no-border" style="text-align: right; white-space: nowrap;">Rp.</td>
                                <td class="no-border" style="text-align: right; padding-right: 2rem;">
                                    {{ number_format($amount ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td class="no-border">Jumlah</td>
                            <td class="no-border" style="text-align: right;"><strong>Rp.</strong>
                            </td>
                            <td class="no-border" style="text-align: right; padding-right: 2rem;">
                                <strong>{{ number_format($grandTotal ?? 0, 0, ',', '.') }}</strong>
                            </td>
                            <td class="no-border">&nbsp;</td>
                        </tr>

                        <tr>
                            <td class="no-border">
                                {{ $accumulatedCosts[0]->comment_ppn == '' ? 'PPN' : 'PPN ' . $accumulatedCosts[0]->comment_ppn }}
                            </td>
                            <td class="no-border" style="text-align: right;">Rp.</td>
                            <td class="no-border" style="text-align: right; padding-right: 2rem;">
                                {{ number_format($accumulatedCosts->sum('nilai_ppn'), 0, ',', '.') }}
                            </td>
                        </tr>

                        <tr>
                            <td class="no-border">Jumlah Total</td>
                            <td style="text-align: right; font-weight: bold;">
                                <strong>Rp.</strong>
                            </td>
                            <td
                                style="display: inline-block; width: 85%; border-top: 1px solid #000000; padding-top: 0.5rem; font-weight: bold; text-align: right; padding-right: 2rem;">
                                <strong>{{ number_format($accumulatedCosts->sum('total'), 0, ',', '.') }}</strong>
                            </td>
                        </tr>

                    </table>
                </div>
            </div>

        </div>

        <!-- Jumlah dalam kotak -->
        <table style="width: 30%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid black;">
            <tr>
                <td
                    style="padding: 5px 10px; text-align: left; font-weight: bold; font-style: italic; vertical-align: middle; height: 30px;">
                    Rp.</td>
                <td
                    style="padding: 5px 10px; text-align: right; font-weight: bold; font-style: italic; vertical-align: middle; height: 30px;">
                    {{ number_format($accumulatedCosts->sum('total'), 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- Pembayaran -->
        <div class="signature-container">
            <table width="100%">
                <tr>
                    <td rowspan="6"></td>
                    <td class="no-border" colspan="3">Pembayaran dapat ditransfer melalui:</td>
                    <td colspan="2" style="border-bottom: none; text-align: center; vertical-align: middle;">
                        Jakarta, {{ \Carbon\Carbon::parse($document->created_at)->translatedFormat('d F Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="no-border">Bank</td>
                    <td class="no-border">: &nbsp;&nbsp;{{ $document->bankAccount->bank_name ?? '-' }}</td>
                    <td class="no-border"></td>
                    <td colspan="2" rowspan="3"
                        style="text-align: center; border-top: none; border-bottom: none;">
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
                    <td class="no-border-top-side" style="vertical-align: top;">
                        Email
                    </td>
                    <td class="no-border-top-side" style="vertical-align: top;">
                        : &nbsp;&nbsp;invoice_center@pt-kpusahatama.com
                    </td>
                    <td class="no-border-top-side" style="vertical-align: top;">

                    </td>
                    <td colspan="2"
                        style="border-top: none; border-bottom: none; text-align: center; font-weight: normal; padding-bottom: 1rem;">
                        <br><br>
                        <strong>Sutaryo</strong><br>Direktur Keuangan dan Administrasi
                    </td>
                </tr>
            </table>
        </div>

        <br><br><br><br><br><br><br><br><br><br><br>

    </div>

    {{-- Footer --}}
    <div class="footer" style="position: absolute; bottom: 0; width: 100%; font-size: 10px; margin-top: 8px;">
        <table border="0" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="width: 30%; border: none; vertical-align: top;">
                    <strong>PT. KARYA PRIMA USAHATAMA<br><em>melayani & memahami</em></strong>
                </td>
                <td style="width: 30%; border: none; vertical-align: top;">
                    RUKO KETAPANG INDAH BLOK A2 NO.8<br>Jl. K.H. Zainul Arifin<br>Jakarta Barat - 11140<br>Indonesia
                </td>
                <td style="width: 30%; border: none; vertical-align: top;">
                    <strong>T</strong>: +62 21-6343 558 <br>
                    <strong>E</strong>: contact@pt-kpusahatama.com
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
