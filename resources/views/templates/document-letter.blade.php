<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Permohonan Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            min-height: 100vh;
            position: relative;
            padding-bottom: 120px;
            /* Ruang untuk tanda tangan dan footer */
        }

        .text-smaller {
            font-size: 10px;
        }

        .border-table td,
        .border-table th {
            border: 1px solid #ddd;
            padding: 4px 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-top: 10px;
        }

        td {
            padding-bottom: 5px;
        }

        .justify-text {
            text-align: justify;
            width: 100%;
            margin: auto;
        }

        .signature-container {
            position: absolute;
            bottom: 257px;
            width: 100%;
        }

        .signature-name {
            margin-top: 60px;
            margin-left: 60px;
        }

        .signature {
            margin-top: 18px;
            margin-bottom: 40px;
            text-align: left;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .footer td {
            vertical-align: top;
        }

        .content-wrap {
            padding-bottom: 180px;
            /* Sesuaikan dengan tinggi tanda tangan + footer */
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

    <div class="content-wrap">
        <!-- Header -->
        <div class="flex justify-start items-center pb-2" style="margin-bottom: 30px;">
            <img src="file://{{ public_path('images/logo-kpu-ls.png') }}" alt="Logo KPU"
                style="height: 50px; width: auto;">
        </div>

        <!-- Surat Detail -->
        <div class="text-smaller">
            <table border="0">
                <tr>
                    <td style="width: 10%; border: none;">Nomor</td>
                    <td style="width: 90%; border: none;">: {{ $document->letter_number ?? 'Nomor surat tidak ada' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 10%; border: none;">Sifat</td>
                    <td style="width: 90%; border: none;">: {{ $bagian ?? 'Penting' }}</td>
                </tr>
                <tr>
                    <td style="width: 10%; border: none;">Lampiran</td>
                    <td style="width: 90%; border: none;">: {{ $bagian ?? '1 (Satu) Set' }}</td>
                </tr>
                <tr>
                    <td style="width: 10%; border: none; vertical-align: top;">Perihal</td>
                    <td style="width: 90%; border: none; vertical-align: top;">
                        <div style="display: inline-block; vertical-align: top;">:</div>
                        <div style="display: inline-block; width: calc(100% - 10px);">
                            <strong>{{ $document->letter_subject ?? '-' }}
                                {{ $document->period ? '-' . $document->period : '' }}</strong>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Tanggal --}}
        <table border="0">
            <td style="width: 100%; border: none;">
                Jakarta, {{ \Carbon\Carbon::parse($document->created_at)->translatedFormat('d F Y') }}
            </td>
        </table>

        {{-- Kepada yth --}}
        <table border="0">
            <tr>
                <td style="width: 100%; border: none;">Kepada Yth:</td>
            </tr>
            <tr>
                <td style="width: 100%; border: none;"><strong>{{ $contract->employee_name ?? 'NULL' }}</strong></td>
            </tr>
            <tr>
                <td style="width: 100%; border: none;">{{ $contract->address ?? 'NULL' }}</td>
            </tr>
        </table>

        <!-- Isi Surat -->
        <div class="mt-4 text-smaller justify-text leading-relaxed">
            Berdasarkan {{ $contract->category ?? 'NULL' }} nomor {{ $contract->contract_number ?? 'NULL' }} tanggal
            {{ \Carbon\Carbon::parse($contract->contract_date)->translatedFormat('d F Y') }} antara
            {{ $contract->employee_name ?? 'NULL' }} dengan PT Karya Prima Usahatama tentang
            {{ $contract->title ?? 'NULL' }}{{ $document->reference_document ? ', ' . $document->reference_document : '' }},
            dengan ini kami mengajukan
            permohonan pembayaran pekerjaan tersebut, dengan bukti perincian terlampir.
        </div>

        <!-- Detail Pembayaran -->
        <table class="w-full mt-4 text-smaller" style="border-collapse: collapse;">
            <tr>
                <td class="w-6">1.</td>
                <td class="w-32">Kwitansi</td>
                <td class="w-2">:</td>
                <td class="w-32">{{ $document->receipt_number ?? 'NULL' }}</td>
                <td class="w-16 text-right pl-2">Sebesar</td>
                <td class="w-10 text-right pl-2">Rp</td>
                <td class="w-24 text-right pl-2">
                    {{ number_format($accumulatedCosts->sum('total'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="w-6">2.</td>
                <td class="w-32">Invoice</td>
                <td class="w-2">:</td>
                <td class="w-32">{{ $document->invoice_number ?? 'NULL' }}</td>
                <td class="w-16 text-right pl-2">Sebesar</td>
                <td class="w-10 text-right pl-2">Rp</td>
                <td class="w-24 text-right pl-2">
                    {{ number_format($accumulatedCosts->sum('total'), 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- Informasi Pembayaran -->
        <div class="mt-2 text-smaller">
            <div>
                Pembayaran dapat ditransfer melalui:
            </div>
            <table border="0">
                <tr>
                    <td style="width: 10%; border: none;">Bank</td>
                    <td style="width: 90%; border: none;">: {{ $document->bankAccount->bank_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="width: 10%; border: none;">No. Rekening</td>
                    <td style="width: 90%; border: none;">: {{ $document->bankAccount->account_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="width: 10%; border: none; vertical-align: top;">Cabang</td>
                    <td style="width: 90%; border: none;">: {{ $document->bankAccount->branch ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="width: 10%; border: none;">Atas Nama</td>
                    <td style="width: 90%; border: none;">: {{ $bagian ?? 'PT. Karya Prima Usahatama' }}</td>
                </tr>
                <tr>
                    <td style="width: 10%; border: none;">Email</td>
                    <td style="width: 90%; border: none;">: {{ $bagian ?? 'invoice_center@pt-kpusahatama.com' }}</td>
                </tr>
            </table>
        </div>

        <div class="mt-4 text-smaller text-justify leading-relaxed">
            <p>
                Demikian kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.
            </p>
        </div>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature-container">
        <div class="signature">
            <div>
                <p>Direktur Keuangan dan Administrasi</p>
                <br />
                <br />
                <div class="signature-name">
                    <p><strong>Sutaryo</strong></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <table border="0">
            <tr>
                <td style="width: 30%; border: none;">
                    <strong>PT. KARYA PRIMA USAHATAMA<br><em>melayani & memahami</em></strong>
                </td>
                <td style="width: 30%; border: none;">
                    RUKO KETAPANG INDAH BLOK A2 NO.8<br>Jl. K.H. Zainul Arifin<br>Jakarta Barat - 11140<br>Indonesia
                </td>
                <td style="width: 30%; border: none;">
                    <strong>T</strong>: +62 21-6343 558 <br> <strong>E</strong>:
                    contact@pt-kpusahatama.com
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
