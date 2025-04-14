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
            margin-bottom: 20px;
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
                <td rowspan="8" style="vertical-align: top;">1</td> <!-- Kolom pertama (No) -->

                <td colspan="3" style=" border-bottom: none;">{{ $document->letter_subject ?? '-' }} -
                    {{ $document->period ?? '-' }}</td> <!-- Keterangan -->

                <td style="border-right:none; border-bottom: none;">Rp</td> <!-- Simbol Rupiah -->
                <td style="text-align: right; border-left:none; border-bottom: none;">
                    {{ number_format($accumulatedCosts->sum('dpp'), 0, ',', '.') }}</td>
                <!-- Jumlah, rata kanan agar lebih rapi -->
            </tr>

            <tr>
                <td class="no-border">{{ $accumulatedCosts[0]->account_name ?? '-' }}</td>
                <td class="no-border">Rp.</td>
                <td style="border-left: none; border-top: none; border-bottom: none;">
                    {{ number_format($accumulatedCosts->sum('dpp'), 0, ',', '.') }}</td>
                <td class="no-border">&nbsp;</td>
                <td style="border-left: none; border-top: none; border-bottom: none;">&nbsp;</td>
            </tr>
            <tr>
                <td class="no-border">Jumlah</td>
                <td class="no-border"><strong>Rp.</strong></td>
                <td style="border-left: none; border-top: none; border-bottom: none;">
                    <strong>{{ number_format($accumulatedCosts->sum('dpp'), 0, ',', '.') }}</strong>
                </td>
                <td class="no-border">&nbsp;</td>
                <td style="border-left: none; border-top: none; border-bottom: none;">&nbsp;</td>
            </tr>
            <tr>
                <td class="no-border">{{ $accumulatedCosts[0]->comment_ppn == '' ? 'PPN' : $accumulatedCosts[0]->comment_ppn }}</td>
                <td class="no-border">Rp.</td>
                <td style="border-left: none; border-top: none; border-bottom: none;">
                    {{ number_format($accumulatedCosts->sum('nilai_ppn'), 0, ',', '.') }}</td>
                <td class="no-border">&nbsp;</td>
                <td style="border-left: none; border-top: none; border-bottom: none;">&nbsp;</td>
            </tr>
            <tr>
                <td class="no-border-top-side">Jumlah Total</td>
                <td class="no-border-top-side">Rp.</td>
                <td
                    style="border-left: none; border-top:none; border-right: 1px solid black; border-bottom: 1px solid black; padding: 5px; position: relative;">
                    {{ number_format($accumulatedCosts->sum('total'), 0, ',', '.') }}
                    <div style="position: absolute; top: 0; left: 0; width: 50%; height: 1px; background: black;"></div>
                </td>
                <td class="no-border-top-side">&nbsp;</td>
                <td style="border-left: none; border-top: none;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">Jumlah</td>
                <td style="border-bottom: none; border-right: none;"><strong>Rp</strong></td>
                <td style="text-align: right; border-left: none;">
                    <strong>{{ number_format($accumulatedCosts->sum('dpp'), 0, ',', '.') }}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">PPN</td>
                <td style="border-bottom: none; border-right: none;">Rp</td>
                <td style="text-align: right; border-left: none;">
                    {{ number_format($accumulatedCosts->sum('nilai_ppn'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Jumlah Total</strong></td>
                <td style="border-bottom: none; border-right: none;"><strong>Rp</strong></td>
                <td style="text-align: right;border-left: none;">
                    <strong>{{ number_format($accumulatedCosts->sum('total'), 0, ',', '.') }}</strong>
                </td>
            </tr>
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
                <td class="no-border">{{ $document->bankAccount->bank_name ?? '-' }}</td>
                <td colspan="2" rowspan="3" style="text-align: center; border-top: none; border-bottom: none;">
                    @php
                        $logoPath = public_path('images/dirut-keuangan.png');
                        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                    @endphp
                    <img src="{{ $logoBase64 }}" alt="Logo KPU" width="150">
                </td>
            </tr>
            <tr>
                <td class="no-border">No. Rekening</td>
                <td class="no-border">:</td>
                <td class="no-border">{{ $document->bankAccount->account_number ?? '-' }}</td>
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
                <td class="no-border">{{ $document->bankAccount->account_name ?? '-' }}</td>
                <td colspan="2"
                    style="border-top: none; border-bottom: none; text-align: center; font-weight: normal;">
                    <strong>Sutaryo</strong>
                    <br>
                    Direktur Keuangan dan Administrasi
                </td </tr>
            <tr>
                <td class="no-border-top-side">
                    Email</td>
                <td class="no-border-top-side">
                    :</td>
                <td class="no-border-top-side">
                    contact@kpusahatama.co.id</td>
                <td colspan="2" style="border-top: none;"></td>
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
                    <strong>T</strong>: +62 21-6343 558 <br> <strong>E</strong>: contact@kpusahaatama.co.id
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
