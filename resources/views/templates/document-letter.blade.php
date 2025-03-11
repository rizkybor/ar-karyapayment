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
        .border-table td, .border-table th {
            border: 1px solid #ddd;
            padding: 4px 8px;
        }
    </style>
</head>
<body class="bg-white p-8">

    <!-- Header -->
    <div class="flex justify-start items-center pb-2">
        <img src="file://{{ public_path('images/logo-kpu-ls.png') }}" alt="Logo KPU" style="height: 50px; width: auto;">
    </div>

    <!-- Surat Detail -->
    <div class="mt-2 text-smaller">
        <p><strong>Nomor:</strong> 008070/TEK/SOL/III/2025</p>
        <p><strong>Sifat:</strong> Penting</p>
        <p><strong>Lampiran:</strong> 1 (Satu) Set</p>
        <p><strong>Perihal:</strong> <span class="font-semibold">Permohonan Pembayaran Masa Pemeliharaan dan Progres Akhir Pekerjaan Revitalisasi Area Taman Belakang Kantor PT PGAS Solution Area Head Cirebon</span></p>
    </div>

    <div class="mt-4 text-smaller">
        <p>Jakarta, 11 Februari 2025</p>
        <p class="mt-3 font-bold">Kepada Yth:</p>
        <p class="font-bold">PT PGAS Solution</p>
        <p>Gedung C Lantai 4</p>
        <p>Jl. K.H. Zainul Arifin No. 20</p>
        <p>Jakarta Barat 11140</p>
    </div>

    <!-- Isi Surat -->
    <div class="mt-4 text-smaller text-justify leading-relaxed">
        <p>
            Berdasarkan Surat Perintah Kerja (SPK) antara PT PGAS Solution dengan PT Karya Prima Usahatama No. 000100.SPK/LG.01/PLUP/PGAS/XII/2024 tanggal 03 Desember 2024 tentang Pekerjaan Revitalisasi Area Taman Belakang Kantor PT PGAS Solution Area Head Cirebon, dengan ini kami mengajukan permohonan pembayaran masa pemeliharaan dan progres akhir pekerjaan tersebut, dengan bukti perincian terlampir.
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
            <td class="w-24 text-right">10.545.000</td>
        </tr>
        <tr>
            <td class="w-6">2.</td>
            <td class="w-32">Invoice</td>
            <td class="w-2">:</td>
            <td class="w-[220px]">No. 002260/INV/SOL/III/2025</td>
            <td class="w-16 text-right">Sebesar</td>
            <td class="w-10 text-right">Rp</td>
            <td class="w-24 text-right">10.545.000</td>
        </tr>
    </table>

    <!-- Informasi Pembayaran -->
    <div class="mt-4 text-smaller">
        <p><strong>Pembayaran dapat ditransfer melalui:</strong></p>
        <p><strong>Bank:</strong> PT. Bank Mandiri (Persero) Tbk</p>
        <p><strong>No. Rekening:</strong> 115.00.9999666.4</p>
        <p><strong>Cabang:</strong> KK Jakarta Gedung PGN Pusat</p>
        <p><strong>Atas Nama:</strong> PT. Karya Prima Usahatama</p>
        <p><strong>Email:</strong> contact@kpusahaatama.co.id</p>
    </div>

    <!-- Tanda Tangan -->
    <div class="mt-10 flex justify-between items-center text-smaller">
        <div>
            <p class="text-smaller">Hormat kami,</p>
            <p class="mt-10 font-bold">Sutaryo</p>
            <p>Direktur Keuangan dan Administrasi</p>
        </div>
        <div>
            <img src="{{ asset('images/stamp.png') }}" alt="Cap Perusahaan" class="h-14">
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-10 text-center text-xs text-gray-600 border-t pt-4">
        <p>PT. Karya Prima Usahatama</p>
        <p>RUKO KETAPANG INDAH BLOK A2 NO.8</p>
        <p>Jl. K.H. Zainul Arifin, Jakarta Barat - 11140, Indonesia</p>
        <p>T: +62 21-6343 558 | E: contact@kpusahaatama.co.id</p>
    </div>

</body>
</html>