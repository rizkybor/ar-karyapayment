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

        .border-box {
            border: 1px solid black;
            padding: 5px;
        }
    </style>
</head>

<body class="p-8">

    @php
        $statusIsSix = (int) $document->status === 6;
        $isPembendaharaan = auth()->user()->role === 'pembendaharaan';
        $showDraft = $statusIsSix && $isPembendaharaan;
    @endphp

    @if (!$showDraft)
        <!-- Watermark Layer -->
        <div
            style="position: fixed; top: 35%; left: 12%; z-index: -1; opacity: 0.08; font-size: 150px; transform: rotate(-30deg); font-weight: bold; color: #000;">
            DRAFT
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
                        <th style="text-align: left; vertical-align: bottom; padding-bottom: 5px; font-weight: normal;">
                            No. {{ $document->receipt_number ?? 'Nomor surat tidak ada' }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td style="padding: 1; vertical-align: top;">
                            <div class="border-box" style="margin: 1; padding: 1;">
                                <p class="font-semibold" style="margin: 1; padding: 1;">Sudah Terima Dari :</p>
                                <p style="margin: 1; padding: 1;">{{ $contract->employee_name ?? 'NULL' }}</p>
                                <p style="margin: 1; padding: 1;">{{ $contract->address ?? 'NULL' }}</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
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

                <table style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 10px;">
                    @php
                        $totalBiaya = 0;
                    @endphp
                    @foreach ($detailPayments as $payment)
                        <tr>
                            <td class="no-border">{{ $payment->expense_type ?? '-' }}</td>
                            <td class="no-border">Rp.</td>
                            <td class="no-border">{{ number_format($payment->nilai_biaya ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @php
                            $totalBiaya += $payment->nilai_biaya ?? 0;
                        @endphp
                    @endforeach
                    <tr>
                        <td class="no-border">Management Fee</td>
                        <td class="no-border">Rp.</td>
                        <td class="no-border">{{ number_format($accumulatedCosts->sum('nilai_manfee'), 0, ',', '.') }}
                        </td>
                    </tr>

                    @php
                        $grandTotal = $totalBiaya + $accumulatedCosts->sum('nilai_manfee');
                    @endphp
                    <tr>
                        <td class="no-border">Jumlah</td>
                        <td class="no-border"><strong>Rp.</strong></td>
                        <td class="no-border">
                            <strong>
                                {{ number_format($grandTotal, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td class="no-border">&nbsp;</td>

                    </tr>
                    <tr>
                        <td class="no-border">PPN</td>
                        <td class="no-border">Rp.</td>
                        <td class="no-border">{{ number_format($accumulatedCosts->sum('nilai_ppn'), 0, ',', '.') }}
                        </td>

                    </tr>
                    <tr>
                        <td class="no-border">Jumlah Total</td>
                        <td style="border: none; position: relative;">
                            <strong>Rp.</strong>
                            <div
                                style="position: absolute; top: 0; right: 0; width: 50%; height: 1px; background: black;">
                            </div>
                        </td>
                        <td
                            style="border-left: none; border-bottom: none; border-top:none; border-right: 1px solid black;  position: relative;">
                            <strong>{{ number_format($accumulatedCosts->sum('total'), 0, ',', '.') }}</strong>
                            <div
                                style="position: absolute; top: 0; left: 0; width: 50%; height: 1px; background: black;">
                            </div>
                        </td>

                    </tr>
                </table>
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
        <table width="100%">
            <tr>
                <td rowspan="6"></td>
                <td class="no-border" colspan="3">Pembayaran dapat ditransfer melalui:</td>
                <td colspan="2" style="border-bottom: none;">Jakarta,
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td class="no-border">Bank</td>
                <td class="no-border">:</td>
                <td class="no-border">PT. Bank Mandiri (Persero) Tbk.</td>
                <td colspan="2" rowspan="3" style="text-align: center; border-top: none; border-bottom: none;">
                    {{-- @php
                        $logoPath = public_path('images/dirut-keuangan.png');
                        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                    @endphp
                    <img src="{{ $logoBase64 }}" alt="Logo KPU" width="150"> --}}
                </td>
            </tr>
            <tr>
                <td class="no-border">No. Rekening</td>
                <td class="no-border">:</td>
                <td class="no-border">115.00.9999666.4</td>
            </tr>
            <tr>
                <td class="no-border">Cabang</td>
                <td class="no-border">:</td>
                <td class="no-border">KK Jakarta Gedung PGN Pusat<br>Jl. KH. Zainul Arifin No. 20<br>Jakarta Barat -
                    11140</td>
            </tr>
            <tr>
                <td class="no-border">Atas Nama</td>
                <td class="no-border">:</td>
                <td class="no-border">PT. Karya Prima Usahatama</td>
                <td colspan="2"
                    style="border-top: none; border-bottom: none; text-align: center; font-weight: normal;">
                    <strong>Sutaryo</strong><br>Direktur Keuangan dan Administrasi
                </td>

            <tr>
                <td class="no-border-top-side">
                    Email</td>
                <td class="no-border-top-side">
                    :</td>
                <td class="no-border-top-side">
                    contact@kpusahatama.co.id</td>
                <td colspan="2" style="border-top: none;"></td>
            </tr>
        </table>

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
                    <strong>E</strong>: contact@kpusahaatama.co.id
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
