<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\NonManfeeDocument;
use App\Models\ManfeeDocument;

use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    /**
     * Generate and download PDF file.
     *
     * @return \Illuminate\Http\Response
     */

    /*
|--------------------------------------------------------------------------
| Default Function
|--------------------------------------------------------------------------
*/
    public function generateLetter($document_id)
    {
        $data = [
            'title' => 'Contoh PDF',
            'content' => 'Ini adalah contoh PDF dalam Laravel 10.'
        ];

        // Load Blade view dari folder templates
        $pdf = Pdf::loadView('templates.document-letter', $data);

        // Download file PDF dengan nama document-letter.pdf
        // return $pdf->download('document-letter.pdf');
        return $pdf->stream('document-letter.pdf');
    }

    public function generateKwitansi()
    {
        $data = [
            'title' => 'Contoh PDF',
            'content' => 'Ini adalah contoh PDF dalam Laravel 10.'
        ];

        // Load Blade view dari folder templates
        $pdf = Pdf::loadView('templates.document-kwitansi', $data);

        // Download file PDF dengan nama document-letter.pdf
        // return $pdf->download('document-kwitansi.pdf');
        return $pdf->stream('document-kwitansi.pdf');
    }

    public function generateInvoice()
    {
        $data = [
            'title' => 'Contoh PDF',
            'content' => 'Ini adalah contoh PDF dalam Laravel 10.'
        ];

        // Load Blade view dari folder templates
        $pdf = Pdf::loadView('templates.document-invoice', $data);

        // Download file PDF dengan nama document-letter.pdf
        // return $pdf->download('document-invoice.pdf');
        return $pdf->stream('document-invoice.pdf');
    }

    /*
|--------------------------------------------------------------------------
| Non Management Fee Function
|--------------------------------------------------------------------------
*/
    public function sanitizeFileName($name)
    {
        // Ganti karakter tidak valid dengan underscore
        return preg_replace('/[\/\\\\:*?"<>|]/', '_', $name);
    }

    public static function nilaiToString($angka)
    {
        $angka = abs($angka);
        $terbilang = "";
        $angka_array = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");

        if ($angka < 12) {
            $terbilang = " " . $angka_array[$angka];
        } else if ($angka < 20) {
            $terbilang = self::nilaiToString($angka - 10) . " Belas";
        } else if ($angka < 100) {
            $terbilang = self::nilaiToString(intval($angka / 10)) . " Puluh" . self::nilaiToString($angka % 10);
        } else if ($angka < 200) {
            $terbilang = " Seratus" . self::nilaiToString($angka - 100);
        } else if ($angka < 1000) {
            $terbilang = self::nilaiToString(intval($angka / 100)) . " Ratus" . self::nilaiToString($angka % 100);
        } else if ($angka < 2000) {
            $terbilang = " Seribu" . self::nilaiToString($angka - 1000);
        } else if ($angka < 1000000) {
            $terbilang = self::nilaiToString(intval($angka / 1000)) . " Ribu" . self::nilaiToString($angka % 1000);
        } else if ($angka < 1000000000) {
            $terbilang = self::nilaiToString(intval($angka / 1000000)) . " Juta" . self::nilaiToString($angka % 1000000);
        } else if ($angka < 1000000000000) {
            $terbilang = self::nilaiToString(intval($angka / 1000000000)) . " Miliar" . self::nilaiToString(fmod($angka, 1000000000));
        } else if ($angka < 1000000000000000) {
            $terbilang = self::nilaiToString(intval($angka / 1000000000000)) . " Triliun" . self::nilaiToString(fmod($angka, 1000000000000));
        }
        return ($terbilang);
    }

    public function showManagerSignature()
    {
        // Contoh: respons dari Dropbox API
        $response = [
            'links' => [
                ['url' => 'https://www.dropbox.com/s/abcd1234/signature.png?dl=0']
            ]
        ];

        // Pastikan response dan URL tersedia
        if (isset($response['links'][0]['url'])) {
            $originalLink = $response['links'][0]['url'];

            // Ubah link menjadi direct download
            $directLink = str_replace("www.dropbox.com", "dl.dropboxusercontent.com", $originalLink);

            // Pastikan parameter `?raw=1` ditambahkan
            if (!str_contains($directLink, '?')) {
                $directLink .= '?raw=1';
            } else {
                $directLink .= '&raw=1';
            }

            // $signatureStatus->$field = $directLink;

            // Simpan model jika diperlukan
            // $signatureStatus->save();

            return $directLink;
        }

        return null; // Jika URL tidak ditemukan
    }

    public function nonManfeeLetter($document_id)
    {
        $document = NonManfeeDocument::with(['contract', 'accumulatedCosts'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
        ];

        // format filename tersusun : letter_number/contract_number/nama_kontraktor 
        $rawFilename = $this->sanitizeFileName($data['document']->letter_number . '_' . $data['contract']->contract_number . '_' . $data['contract']->employee_name);
        $filename = $rawFilename . '.pdf';

        $pdf = Pdf::loadView('templates.document-letter', $data);

        return $pdf->stream($filename);
    }

    public function nonManfeeInvoice($document_id)
    {
        $document = NonManfeeDocument::with(['contract', 'accumulatedCosts'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
        ];

        // format filename tersusun : invoice_number/contract_number/nama_kontraktor 
        $rawFilename = $this->sanitizeFileName($data['document']->invoice_number . '_' . $data['contract']->contract_number . '_' . $data['contract']->employee_name);
        $filename = $rawFilename . '.pdf';

        $pdf = Pdf::loadView('templates.document-invoice', $data);

        return $pdf->stream($filename);
    }

    public function nonManfeeKwitansi($document_id)
    {
        $document = NonManfeeDocument::with(['contract', 'accumulatedCosts'])->findOrFail($document_id);

        // Pastikan accumulatedCosts tidak kosong untuk menghindari error
        $firstCost = $document->accumulatedCosts->first();

        if (!$firstCost) {
            return back()->with('error', 'Dokumen tidak memiliki akumulasi biaya.');
        }

        // Hitung nilai terbilang dari total
        $terbilang = $this->nilaiToString($firstCost->total);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'terbilang' => $terbilang
        ];

        // Format filename: receipt_number_contract_number_nama_kontraktor.pdf
        $rawFilename = $this->sanitizeFileName(
            $document->receipt_number . '_' .
                $document->contract->contract_number . '_' .
                $document->contract->employee_name
        );

        $filename = $rawFilename . '.pdf';

        // Buat dan tampilkan PDF
        $pdf = Pdf::loadView('templates.document-kwitansi', $data);

        return $pdf->stream($filename);
    }

    /*
|--------------------------------------------------------------------------
| Management Fee Function
|--------------------------------------------------------------------------
*/

    public function ManfeeLetter($document_id)
    {
        $document = ManfeeDocument::with(['contract', 'accumulatedCosts'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
        ];

        // format filename tersusun : letter_number/contract_number/nama_kontraktor 
        $rawFilename = $this->sanitizeFileName($data['document']->letter_number . '_' . $data['contract']->contract_number . '_' . $data['contract']->employee_name);
        $filename = $rawFilename . '.pdf';

        $pdf = Pdf::loadView('templates.management-fee.document-letter', $data);

        return $pdf->stream($filename);
    }

    public function ManfeeInvoice($document_id)
    {
        $document = ManfeeDocument::with(['contract', 'detailPayments', 'accumulatedCosts'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'detailPayments' => $document->detailPayments
        ];

        // format filename tersusun : invoice_number/contract_number/nama_kontraktor 
        $rawFilename = $this->sanitizeFileName($data['document']->invoice_number . '_' . $data['contract']->contract_number . '_' . $data['contract']->employee_name);
        $filename = $rawFilename . '.pdf';

        $pdf = Pdf::loadView('templates.management-fee.document-invoice', $data);

        return $pdf->stream($filename);
    }

    public function ManfeeKwitansi($document_id)
    {
        $document = ManfeeDocument::with(['contract', 'detailPayments', 'accumulatedCosts'])->findOrFail($document_id);

        // Pastikan accumulatedCosts tidak kosong untuk menghindari error
        $firstCost = $document->accumulatedCosts->first();

        if (!$firstCost) {
            return back()->with('error', 'Dokumen tidak memiliki akumulasi biaya.');
        }

        // Hitung nilai terbilang dari total
        $terbilang = $this->nilaiToString($firstCost->total);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'terbilang' => $terbilang,
            'detailPayments' => $document->detailPayments
        ];

        // Format filename: receipt_number_contract_number_nama_kontraktor.pdf
        $rawFilename = $this->sanitizeFileName(
            $document->receipt_number . '_' .
                $document->contract->contract_number . '_' .
                $document->contract->employee_name
        );

        $filename = $rawFilename . '.pdf';

        // Buat dan tampilkan PDF
        $pdf = Pdf::loadView('templates.management-fee.document-kwitansi', $data);

        return $pdf->stream($filename);
    }
}
