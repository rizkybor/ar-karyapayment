<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\NonManfeeDocument;
use App\Models\ManfeeDocument;
use ZipArchive;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\PrivyController;



class PDFController extends Controller
{
    /**
     * Generate and download PDF file.
     *
     * @return \Illuminate\Http\Response
     */

    /*
|--------------------------------------------------------------------------
| Global Function
|--------------------------------------------------------------------------
*/
    private function fetchSignedDocumentUrl($documentId, $category, $type)
    {

        $privyCtrl = new PrivyController();
        $signedUrl = $privyCtrl->getSignedDocumentUrl($documentId, $category, $type);

        if (!$signedUrl) {
            throw new \Exception("Dokumen {$type} belum ditandatangani atau tidak ditemukan.");
        }

        return $signedUrl;
    }

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

    /*
|--------------------------------------------------------------------------
| Non Management Fee PDF (Letter, Invoice, Kwitansi) View Single File
|--------------------------------------------------------------------------
*/

    public function nonManfeeLetter($document_id)
    {
        $document = NonManfeeDocument::with(['contract', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts
        ];

        // format filename tersusun : letter_number/contract_number/nama_kontraktor 
        $rawFilename = $this->sanitizeFileName($data['document']->letter_number . '_' . $data['contract']->contract_number . '_' . $data['contract']->employee_name);
        $filename = $rawFilename . '.pdf';

        $pdf = Pdf::loadView('templates.document-letter', $data);

        return $pdf->stream($filename);
    }

    public function nonManfeeInvoice($document_id)
    {
        $document = NonManfeeDocument::with(['contract', 'detailPayments', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);

        $firstCost = $document->accumulatedCosts->first();
        if (!$firstCost) {
            return back()->with('error', 'Dokumen tidak memiliki akumulasi biaya.');
        }

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'detailPayments' => $document->detailPayments
        ];

        // format filename tersusun : invoice_number/contract_number/nama_kontraktor 
        $rawFilename = $this->sanitizeFileName($data['document']->invoice_number . '_' . $data['contract']->contract_number . '_' . $data['contract']->employee_name);
        $filename = $rawFilename . '.pdf';

        $pdf = Pdf::loadView('templates.document-invoice', $data);

        return $pdf->stream($filename);
    }

    public function nonManfeeKwitansi($document_id)
    {
        $document = NonManfeeDocument::with(['contract', 'detailPayments', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);

        // Pastikan accumulatedCosts tidak kosong untuk menghindari error
        $firstCost = $document->accumulatedCosts->first();
        if (!$firstCost) {
            return back()->with('error', 'Dokumen tidak memiliki akumulasi biaya.');
        }

        // Pembulatan keatas
        $totalRounded = round($firstCost->total);

        // Hitung nilai terbilang dari total
        $terbilang = $this->nilaiToString($totalRounded);

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
        $pdf = Pdf::loadView('templates.document-kwitansi', $data);

        return $pdf->stream($filename);
    }

    /*
|--------------------------------------------------------------------------
| Non Management Fee PDF (Letter, Invoice, Kwitansi) BASE 64 before Upload Privy
|--------------------------------------------------------------------------
*/

    public function nonManfeeLetterBase64($document_id, $disableWatermark = true): string
    {
        $document = NonManfeeDocument::with(['contract', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'disableWatermark' => $disableWatermark
        ];

        $pdf = PDF::loadView('templates.document-letter', $data);
        $pdfOutput = $pdf->output();
        $base64 = base64_encode($pdfOutput);

        return $base64;
    }

    public function nonManfeeInvoiceBase64($document_id, $disableWatermark = true): string
    {
        $document = NonManfeeDocument::with(['contract', 'detailPayments', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'detailPayments' => $document->detailPayments,
            'disableWatermark' => $disableWatermark
        ];

        $pdf = PDF::loadView('templates.document-invoice', $data);
        $pdfOutput = $pdf->output();
        $base64 = base64_encode($pdfOutput);

        return $base64;
    }

    public function nonManfeeKwitansiBase64($document_id, $disableWatermark = true): string
    {
        $document = NonManfeeDocument::with([
            'contract',
            'detailPayments',
            'accumulatedCosts',
            'bankAccount'
        ])->findOrFail($document_id);

        // Pastikan ada accumulated cost
        $firstCost = $document->accumulatedCosts->first();

        if (!$firstCost) {
            throw new \Exception('Dokumen tidak memiliki akumulasi biaya.');
        }

        // Pembulatan keatas
        $totalRounded = round($firstCost->total);

        // Hitung nilai terbilang
        $terbilang = $this->nilaiToString($totalRounded);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'terbilang' => $terbilang,
            'detailPayments' => $document->detailPayments,
            'disableWatermark' => $disableWatermark
        ];

        $pdf = PDF::loadView('templates.document-kwitansi', $data);
        $pdfOutput = $pdf->output();
        $base64 = base64_encode($pdfOutput);

        return $base64;
    }

    /*
|--------------------------------------------------------------------------
| Non Management Fee PDF (Letter, Invoice, Kwitansi, Attachment & Taxes) EXTRACT ALL ZIP
|--------------------------------------------------------------------------
*/

    public function nonManfeeZip($document_id)
    {
        if (auth()->user()->role !== 'perbendaharaan') {
            return back()->with('error', 'Maaf, Anda tidak memiliki akses!');
        }

        $document = NonManfeeDocument::with([
            'contract',
            'detailPayments',
            'accumulatedCosts',
            'attachments',
            'taxFiles',
            'bankAccount'
        ])->findOrFail($document_id);

        if ((int) $document->status !== 6) {
            return back()->with('error', 'Dokumen hanya dapat diunduh jika sudah Done');
        }

        // File Name
        $letterName = $this->sanitizeFileName($document->letter_number . '_' . $document->contract->employee_name);
        $invoiceName = $this->sanitizeFileName($document->invoice_number . '_' . $document->contract->employee_name);
        $kwitansiName = $this->sanitizeFileName($document->receipt_number . '_' . $document->contract->employee_name);

        $tempDir = storage_path('app/temp_' . uniqid());
        if (!file_exists($tempDir)) mkdir($tempDir, 0777, true);

        // Generate PDFs
        $letterPdfPath = $tempDir . "/Surat_Permohonan_{$letterName}.pdf";
        $invoicePdfPath = $tempDir . "/Invoice_{$invoiceName}.pdf";
        $kwitansiPdfPath = $tempDir . "/Kwitansi_{$kwitansiName}.pdf";

        try {
            $letterUrl = $this->fetchSignedDocumentUrl($document->id, $document->category, 'letter');
            file_put_contents($letterPdfPath, file_get_contents($letterUrl));

            $invoiceUrl = $this->fetchSignedDocumentUrl($document->id, $document->category, 'invoice');
            file_put_contents($invoicePdfPath, file_get_contents($invoiceUrl));

            $kwitansiUrl = $this->fetchSignedDocumentUrl($document->id, $document->category, 'kwitansi');
            file_put_contents($kwitansiPdfPath, file_get_contents($kwitansiUrl));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // Dropbox files
        $dropbox = new DropboxController();
        $attachments = $document->attachments->pluck('path')->toArray();
        $taxes = $document->taxFiles->pluck('path')->toArray();

        $attachmentFiles = $dropbox->downloadMultipleFromDropbox($attachments, '/attachments/');
        $taxFiles = $dropbox->downloadMultipleFromDropbox($taxes, '/taxes/');

        // Create ZIP
        $rawInvoiceName = $this->sanitizeFileName($document->invoice_number);
        $zipPath = storage_path("app/{$rawInvoiceName}.zip");
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // Add PDFs
            $zip->addFile($letterPdfPath, basename($letterPdfPath));
            $zip->addFile($invoicePdfPath, basename($invoicePdfPath));
            $zip->addFile($kwitansiPdfPath, basename($kwitansiPdfPath));

            // Add Dropbox files
            foreach (array_merge($attachmentFiles, $taxFiles) as $file) {
                if (file_exists($file['path'])) {
                    $zip->addFile($file['path'], $file['name']);
                }
            }

            $zip->close();
        } else {
            return back()->with('error', 'Gagal membuat ZIP.');
        }

        // Cleanup
        foreach ([$letterPdfPath, $invoicePdfPath, $kwitansiPdfPath] as $file) {
            if (file_exists($file)) unlink($file);
        }
        foreach (array_merge($attachmentFiles, $taxFiles) as $file) {
            if (file_exists($file['path'])) unlink($file['path']);
        }
        if (file_exists($tempDir)) rmdir($tempDir);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }


    /*
|--------------------------------------------------------------------------
| Management Fee PDF (Letter, Invoice, Kwitansi) View Single File
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

        // Pastikan accumulatedCosts tidak kosong untuk menghindari error
        $firstCost = $document->accumulatedCosts->first();
        if (!$firstCost) {
            return back()->with('error', 'Dokumen tidak memiliki akumulasi biaya.');
        }

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

        // Pembulatan keatas
        $totalRounded = round($firstCost->total);

        // Hitung nilai terbilang dari total
        $terbilang = $this->nilaiToString($totalRounded);

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

    /*
|--------------------------------------------------------------------------
| Management Fee PDF (Letter, Invoice, Kwitansi) BASE 64 before Upload Privy
|--------------------------------------------------------------------------
*/

    public function manfeeLetterBase64($document_id, $disableWatermark = true): string
    {
        $document = ManfeeDocument::with(['contract', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'disableWatermark' => $disableWatermark
        ];

        $pdf = PDF::loadView('templates.management-fee.document-letter', $data);
        $pdfOutput = $pdf->output();
        $base64 = base64_encode($pdfOutput);

        return $base64;
    }

    public function manfeeInvoiceBase64($document_id, $disableWatermark = true): string
    {
        $document = ManfeeDocument::with(['contract', 'detailPayments', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'detailPayments' => $document->detailPayments,
            'disableWatermark' => $disableWatermark
        ];

        $pdf = PDF::loadView('templates.management-fee.document-invoice', $data);
        $pdfOutput = $pdf->output();
        $base64 = base64_encode($pdfOutput);

        return $base64;
    }

    public function manfeeKwitansiBase64($document_id, $disableWatermark = true): string
    {
        $document = ManfeeDocument::with([
            'contract',
            'detailPayments',
            'accumulatedCosts',
            'bankAccount'
        ])->findOrFail($document_id);

        // Pastikan ada accumulated cost
        $firstCost = $document->accumulatedCosts->first();

        if (!$firstCost) {
            throw new \Exception('Dokumen tidak memiliki akumulasi biaya.');
        }

        // Pembulatan keatas
        $totalRounded = round($firstCost->total);

        // Hitung nilai terbilang
        $terbilang = $this->nilaiToString($totalRounded);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'terbilang' => $terbilang,
            'detailPayments' => $document->detailPayments,
            'disableWatermark' => $disableWatermark
        ];

        $pdf = PDF::loadView('templates.management-fee.document-kwitansi', $data);
        $pdfOutput = $pdf->output();
        $base64 = base64_encode($pdfOutput);

        return $base64;
    }

    /*
|--------------------------------------------------------------------------
| Management Fee PDF (Letter, Invoice, Kwitansi, Attachment & Taxes) EXTRACT ALL ZIP
|--------------------------------------------------------------------------
*/

    public function ManfeeZip($document_id)
    {
        if (auth()->user()->role !== 'perbendaharaan') {
            return back()->with('error', 'Maaf, Anda tidak memiliki akses!');
        }

        $document = ManfeeDocument::with([
            'contract',
            'detailPayments',
            'accumulatedCosts',
            'attachments',
            'taxFiles',
            'bankAccount'
        ])->findOrFail($document_id);

        if ((int) $document->status !== 6) {
            return back()->with('error', 'Dokumen hanya dapat diunduh jika sudah Done');
        }

        // File Name
        $letterName = $this->sanitizeFileName($document->letter_number . '_' . $document->contract->employee_name);
        $invoiceName = $this->sanitizeFileName($document->invoice_number . '_' . $document->contract->employee_name);
        $kwitansiName = $this->sanitizeFileName($document->receipt_number . '_' . $document->contract->employee_name);

        $tempDir = storage_path('app/temp_' . uniqid());
        if (!file_exists($tempDir)) mkdir($tempDir, 0777, true);

        // Generate PDFs
        $letterPdfPath = $tempDir . "/Surat_Permohonan_{$letterName}.pdf";
        $invoicePdfPath = $tempDir . "/Invoice_{$invoiceName}.pdf";
        $kwitansiPdfPath = $tempDir . "/Kwitansi_{$kwitansiName}.pdf";

        try {
            $letterUrl = $this->fetchSignedDocumentUrl($document->id, $document->category, 'letter');
            file_put_contents($letterPdfPath, file_get_contents($letterUrl));

            $invoiceUrl = $this->fetchSignedDocumentUrl($document->id, $document->category, 'invoice');
            file_put_contents($invoicePdfPath, file_get_contents($invoiceUrl));

            $kwitansiUrl = $this->fetchSignedDocumentUrl($document->id, $document->category, 'kwitansi');
            file_put_contents($kwitansiPdfPath, file_get_contents($kwitansiUrl));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // Dropbox files
        $dropbox = new DropboxController();
        $attachments = $document->attachments->pluck('path')->toArray();
        $taxes = $document->taxFiles->pluck('path')->toArray();

        $attachmentFiles = $dropbox->downloadMultipleFromDropbox($attachments, '/attachments/');
        $taxFiles = $dropbox->downloadMultipleFromDropbox($taxes, '/taxes/');

        // Create ZIP
        $rawInvoiceName = $this->sanitizeFileName($document->invoice_number);
        $zipPath = storage_path("app/{$rawInvoiceName}.zip");
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // Add PDFs
            $zip->addFile($letterPdfPath, basename($letterPdfPath));
            $zip->addFile($invoicePdfPath, basename($invoicePdfPath));
            $zip->addFile($kwitansiPdfPath, basename($kwitansiPdfPath));

            // Add Dropbox files
            foreach (array_merge($attachmentFiles, $taxFiles) as $file) {
                if (file_exists($file['path'])) {
                    $zip->addFile($file['path'], $file['name']);
                }
            }

            $zip->close();
        } else {
            return back()->with('error', 'Gagal membuat ZIP.');
        }

        // Cleanup
        foreach ([$letterPdfPath, $invoicePdfPath, $kwitansiPdfPath] as $file) {
            if (file_exists($file)) unlink($file);
        }
        foreach (array_merge($attachmentFiles, $taxFiles) as $file) {
            if (file_exists($file['path'])) unlink($file['path']);
        }
        if (file_exists($tempDir)) rmdir($tempDir);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
