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
        .table-border td, .table-border th {
            border: 1px solid black;
            padding: 4px;
        }
    </style>
</head>
<body class="bg-white p-8">

    <!-- Header -->
    <div class="flex justify-between items-start border-b pb-2">
        <div class="font-bold text-lg">LOGO</div>
        <div class="text-right">
            <p class="font-bold">INVOICE</p>
            <p>No. 002260/INV/SOL/III/2025</p>
        </div>
    </div>

    <!-- Penerima -->
    <div class="border-box mt-4 w-1/2">
        <p><strong>Kepada Yth:</strong></p>
        <p>PT PGAS Solution</p>
        <p>Gedung C Lantai 4</p>
        <p>Jl. K.H. Zainul Arifin No. 20</p>
        <p>Jakarta Barat 11140</p>
    </div>

    <!-- Kwitansi dan Tanggal -->
    <table class="w-full table-border mt-4 text-sm border-collapse">
        <tr>
            <td class="w-1/2"><strong>Kwitansi</strong></td>
            <td class="w-1/2">No. 002260/KW/KPU/SOL/III/2025</td>
        </tr>
        <tr>
            <td class="w-1/2"><strong>Tanggal</strong></td>
            <td class="w-1/2">11 Februari 2025</td>
        </tr>
    </table>

    <!-- Detail Pekerjaan -->
    <table class="w-full table-border mt-4 text-sm border-collapse">
        <tr class="font-bold text-center">
            <th class="py-1 w-8">No</th>
            <th class="py-1 text-left">Keterangan</th>
            <th class="py-1 w-32 text-right">Jumlah</th>
        </tr>
        <tr>
            <td class="py-1 text-center">1</td>
            <td class="py-1">
                Masa Pemeliharaan dan Progres Akhir Pekerjaan Revitalisasi Area Taman Belakang Kantor PT PGAS Solution Area Head Cirebon
            </td>
            <td class="py-1 text-right">Rp 9.500.000</td>
        </tr>
        <tr>
            <td></td>
            <td class="py-1">Biaya Personil</td>
            <td class="py-1 text-right">Rp 9.500.000</td>
        </tr>
        <tr>
            <td></td>
            <td class="py-1">Jumlah</td>
            <td class="py-1 text-right">Rp 9.500.000</td>
        </tr>
        <tr>
            <td></td>
            <td class="py-1">PPN 12%</td>
            <td class="py-1 text-right">Rp 1.045.000</td>
        </tr>
        <tr class="font-bold">
            <td></td>
            <td class="py-1">Jumlah Total</td>
            <td class="py-1 text-right">Rp 10.545.000</td>
        </tr>
    </table>

    <!-- Total -->
    <table class="w-full table-border mt-4 text-sm border-collapse">
        <tr class="font-bold">
            <td class="py-1 w-3/4 text-right">Jumlah</td>
            <td class="py-1 text-right w-1/4">Rp 9.500.000</td>
        </tr>
        <tr>
            <td class="py-1 w-3/4 text-right">PPN 12%</td>
            <td class="py-1 text-right w-1/4">Rp 1.045.000</td>
        </tr>
        <tr class="font-bold">
            <td class="py-1 w-3/4 text-right">Jumlah Total</td>
            <td class="py-1 text-right w-1/4">Rp 10.545.000</td>
        </tr>
    </table>

    <!-- Info Pembayaran -->
    <div class="border-box mt-4">
        <p><strong>Pembayaran dapat ditransfer melalui :</strong></p>
        <p><strong>Bank:</strong> PT. Bank Mandiri (Persero) Tbk</p>
        <p><strong>No. Rekening:</strong> 115.00.9999666.4</p>
        <p><strong>Cabang:</strong> KK Jakarta Gedung PGN Pusat</p>
        <p>Jl. KH. Zainul Arifin No. 20, Jakarta Barat - 11140</p>
        <p><strong>Atas Nama:</strong> PT. Karya Prima Usahatama</p>
        <p><strong>Email:</strong> contact@kpusahaatama.co.id</p>
    </div>

    <!-- Tanda Tangan -->
    <div class="flex justify-between items-start mt-4">
        <div class="text-sm">
            <p>Jakarta, 11 Februari 2025</p>
            <div class="font-bold text-lg mt-4">MATERAI</div>
        </div>
        <div class="text-center">
            <p class="font-bold">Sutaryo</p>
            <p class="text-sm">Direktur Keuangan Dan Administrasi</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-10 text-center text-xs text-gray-600 border-t pt-4">
        <p>PT. KARYA PRIMA USAHATAMA</p>
        <p>RUKO KETAPANG INDAH BLOK A2 NO.8</p>
        <p>Jl. K.H. Zainul Arifin, Jakarta Barat - 11140, INDONESIA</p>
        <p>T: +62 21-6343 558 | E: contact@kpusahaatama.co.id</p>
    </div>

</body>
</html>