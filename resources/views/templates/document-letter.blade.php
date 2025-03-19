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
        }

        td {
            padding-bottom: 5px;
        }

        .justify-text {
            text-align: justify;
            width: 100%;
            margin: auto;
        }

        .signature {
            margin-top: 40px;
            margin-bottom: 40px;
            text-align: left;
        }

        .signature div {
            display: block;
            width: 100%;
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
    </style>
</head>

<body class="bg-white p-8">

    <!-- Header -->
    <div class="flex justify-start items-center pb-2" style="margin-bottom: 30px;">
        <img src="file://{{ public_path('images/logo-kpu-ls.png') }}" alt="Logo KPU" style="height: 50px; width: auto;">
    </div>


    <!-- Surat Detail -->
    <div class="text-smaller">
        <table border="0">
            <tr>
                <td style="width: 10%; border: none;">Nomor</td>
                <td style="width: 90%; border: none;">: {{ $bagian ?? 'NO Surat' }}</td>
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
                        <strong>{{ $bagian ?? 'Permohonan Pembayaran Masa Pemeliharaan dan Progres Akhir Pekerjaan Revitalisasi Area Taman Belakang Kantor PT Jendela Kode Area Head Cirebon' }}</strong>
                    </div>
                </td>
            </tr>

        </table>
    </div>

    {{-- Tanggal --}}

    <table border="0">
        <tr>
            <td style="width: 100%; border: none;">Jakarta, 00 Januari 0000</td>
        </tr>
    </table>

    {{-- Kepada yth --}}

    <table border="0">
        <tr>
            <td style="width: 100%; border: none;">Kepada Yth:</td>
        </tr>
        <tr>
            <td style="width: 100%; border: none;"><strong>PT Jendela Kode</strong></td>
        </tr>
        <tr>
            <td style="width: 100%; border: none;">Gedung C Lantai 4</td>
        </tr>
        <tr>
            <td style="width: 100%; border: none;">L. K.H. Zainul Arifin No. 2</td>
        </tr>
        <tr>
            <td style="width: 100%; border: none;">Jakarta Barat 11140</td>
        </tr>
    </table>


    <!-- Isi Surat -->
    <div class="mt-4 text-smaller justify-text leading-relaxed">
        <p>
            Berdasarkan Surat Perintah Kerja (SPK) antara PT Jendela Kode dengan PT Karya Prima Usahatama No.
            *********/INV/SOL/III/2025 tanggal 00 Desember 0000 tentang Pekerjaan Revitalisasi Area Taman Belakang
            Kantor PT Jendela Kode Area Head Cirebon, dengan ini kami mengajukan permohonan pembayaran masa pemeliharaan
            dan progres akhir pekerjaan tersebut, dengan bukti perincian terlampir.
        </p>
    </div>

    <!-- Detail Pembayaran -->
    <table class="w-full mt-4 text-smaller" style="border-collapse: collapse;">
        <tr>
            <td class="w-6">1.</td>
            <td class="w-32">Kwitansi</td>
            <td class="w-2">:</td>
            <td class="w-[220px]">No. 002260/KW/KPU/SOL/III/2025</td>
            <td class="w-16 text-right">Sebesar</td>
            <td class="w-10 text-right">Rp</td>
            <td class="w-24 text-right">NILAI RUPIAH</td>
        </tr>
        <tr>
            <td class="w-6">2.</td>
            <td class="w-32">Invoice</td>
            <td class="w-2">:</td>
            <td class="w-[220px]">No. *********/INV/SOL/III/2025</td>
            <td class="w-16 text-right">Sebesar</td>
            <td class="w-10 text-right">Rp</td>
            <td class="w-24 text-right">NILAI RUPIAH</td>
        </tr>
    </table>

    <!-- Informasi Pembayaran -->
    <div class="mt-2 text-smaller">
        <p>Pembayaran dapat ditransfer melalui:</p>
        <table border="0">
            <tr>
                <td style="width: 10%; border: none;">Bank</td>
                <td style="width: 90%; border: none;">: {{ $bagian ?? 'PT. Bank Mandiri (Persero) Tbk' }}</td>
            </tr>
            <tr>
                <td style="width: 10%; border: none;">No. Rekening</td>
                <td style="width: 90%; border: none;">: {{ $bagian ?? '115.00.9999666.4' }}</td>
            </tr>
            <tr>
                <td style="width: 10%; border: none; vertical-align: top;">Cabang</td>
                <td style="width: 90%; border: none;">: {{ $bagian ?? 'KK Jakarta Gedung PGN PUSAT' }}</td>
            </tr>
            <tr>
                <td style="width: 10%; border: none;"></td>
                <td style="width: 90%; border: none; padding-left: 6px;">
                    {{ $bagian ?? 'JL. KH. Zainul Arifin No. 20' }}</td>
            </tr>
            <tr>
                <td style="width: 10%; border: none;"></td>
                <td style="width: 90%; border: none; padding-left: 6px;">{{ $bagian ?? 'Jakarta Barat - 11140' }}</td>
            </tr>

            <tr>
                <td style="width: 10%; border: none;">Atas Nama</td>
                <td style="width: 90%; border: none;">: {{ $bagian ?? 'PT. Karya Prima Usahatama' }}</td>
            </tr>
            <tr>
                <td style="width: 10%; border: none;">Email</td>
                <td style="width: 90%; border: none;">: {{ $bagian ?? 'contact@kpusahatama.co.id' }}</td>
            </tr>
        </table>
    </div>

    <div class="mt-4 text-smaller text-justify leading-relaxed">
        <p>
            Demikian kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.
        </p>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature">
        <div>
            <p>Diterktur Keuangan Dan Administrasi</p>
            <br><br>
            <p>_____________________</p>
            <p><strong style="text-decoration: underline;">Sutaryo</strong></p>
        </div>
    </div>

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
