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

                <h3>No. 002260/INV/SOL/ll/2025</h3>
            </td>
        </tr>
    </table>

    <!-- Penerima -->
    <table border="1" style="border-collapse: collapse; width: 50%; margin-left: 0;">
        <tr>
            <td style="border: 1px solid black; padding: 8px; text-align: left;">
                Kepada Yth:<br>
                <strong>PT Gas Solution</strong><br>
                Gedung C Lantai 4<br>
                Jl. K.H. Zainul Arifin No. 2<br>
                Jakarta Barat 11140
            </td>
        </tr>
    </table>

    <!-- Kwitansi dan Tanggal -->
    <table class="w-full mt-4 text-sm border-collapse"
        style="border: 1px solid black; width: 60%; border-collapse: collapse;">
        <tr>
            <td class="w-1/2" style="border: 1px solid black; padding: 8px;"><strong>Kwitansi</strong></td>
            <td class="w-2" style="border: 1px solid black; padding: 8px;">No. 002260/KW/KPU/SOL/III/2025</td>
        </tr>
        <tr>
            <td class="w-1/2" style="border: 1px solid black; padding: 8px;"><strong>Tanggal</strong></td>
            <td class="w-2" style="border: 1px solid black; padding: 8px;">11 Februari 2025</td>
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

                <td colspan="3" style=" border-bottom: none;">Masa Pemeliharaan dan progress akhir dan pekerjaan
                    Revilitas</td> <!-- Keterangan -->

                <td style="border-right:none; border-bottom: none;">Rp</td> <!-- Simbol Rupiah -->
                <td style="text-align: right; border-left:none; border-bottom: none;">9.500.000</td>
                <!-- Jumlah, rata kanan agar lebih rapi -->
            </tr>

            <tr>
                <td class="no-border">Biaya Personil</td>
                <td class="no-border">Rp.</td>
                <td style="border-left: none; border-top: none; border-bottom: none;">9.500.000</td>
                <td class="no-border">&nbsp;</td>
                <td style="border-left: none; border-top: none; border-bottom: none;">&nbsp;</td>
            </tr>
            <tr>
                <td class="no-border">Jumlah</td>
                <td class="no-border"><strong>Rp.</strong></td>
                <td style="border-left: none; border-top: none; border-bottom: none;"><strong>9.500.000</strong></td>
                <td class="no-border">&nbsp;</td>
                <td style="border-left: none; border-top: none; border-bottom: none;">&nbsp;</td>
            </tr>
            <tr>
                <td class="no-border">PPN 12%</td>
                <td class="no-border">Rp.</td>
                <td style="border-left: none; border-top: none; border-bottom: none;">1.045.000</td>
                <td class="no-border">&nbsp;</td>
                <td style="border-left: none; border-top: none; border-bottom: none;">&nbsp;</td>
            </tr>
            <tr>
                <td class="no-border-top-side">Jumlah Total</td>
                <td class="no-border-top-side">Rp.</td>
                <td
                    style="border-left: none; border-top:none; border-right: 1px solid black; border-bottom: 1px solid black; padding: 5px; position: relative;">
                    19.545.000
                    <div style="position: absolute; top: 0; left: 0; width: 50%; height: 1px; background: black;"></div>
                </td>
                <td class="no-border-top-side">&nbsp;</td>
                <td style="border-left: none; border-top: none;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">Jumlah</td>
                <td style="border-bottom: none; border-right: none;"><strong>Rp</strong></td>
                <td style="text-align: right; border-left: none;"><strong>9.500.000</strong></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">PPN 12%</td>
                <td style="border-bottom: none; border-right: none;">Rp</td>
                <td style="text-align: right; border-left: none;">1.045.000</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Jumlah Total</strong></td>
                <td style="border-bottom: none; border-right: none;"><strong>Rp</strong></td>
                <td style="text-align: right;border-left: none;"><strong>10.545.000</strong></td>
            </tr>
            <tr>
                <td rowspan="6"></td>
                <td class="no-border" colspan="3">Pembayaran dapat ditransfer melalui:</td>
                <td colspan="2" style="border-bottom: none;">Jakarta, 11 Februari 2025<br>Direktur Keuangan dan
                    Administrasi
                </td>
            </tr>
            <tr>
                <td class="no-border">Bank</td>
                <td class="no-border">:</td>
                <td class="no-border">PT. Bank Mandiri (Persero) Tbk.</td>
                <td colspan="2" rowspan="3" style="border-top: none; border-bottom: none;">
                    <img src="https://repository-images.githubusercontent.com/8805592/85279ffa-7f4a-4880-8e41-59e8032b0f71"
                        alt="signature" width="150" height="150">
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
                    style="border-top: none; border-bottom: none; text-align: center; font-weight: bold;">
                    Sutaryo
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
