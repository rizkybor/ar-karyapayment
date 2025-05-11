<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\NonManfeeDocument;
use App\Models\ManfeeDocument;
use ZipArchive;
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

    /*
|--------------------------------------------------------------------------
| Non Management Fee PDF (Letter, Invoice, Kwitansi) View
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
        $pdf = Pdf::loadView('templates.document-kwitansi', $data);

        return $pdf->stream($filename);
    }

    /*
|--------------------------------------------------------------------------
| Non Management Fee PDF (Letter, Invoice, Kwitansi) BASE 64
|--------------------------------------------------------------------------
*/

    public function nonManfeeLetterBase64($document_id): string
    {
        $document = NonManfeeDocument::with(['contract', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts
        ];

        $pdf = PDF::loadView('templates.document-letter', $data);
        $pdfOutput = $pdf->output();
        $base64 = base64_encode($pdfOutput);

        return $base64;
    }

    public function nonManfeeInvoiceBase64($document_id): string
    {
        $document = NonManfeeDocument::with(['contract', 'detailPayments', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'detailPayments' => $document->detailPayments
        ];

        $pdf = PDF::loadView('templates.document-invoice', $data);
        $pdfOutput = $pdf->output();
        $base64 = base64_encode($pdfOutput);

        return $base64;
    }

    public function nonManfeeKwitansiBase64($document_id): string
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

        // Hitung nilai terbilang
        $terbilang = $this->nilaiToString($firstCost->total);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'terbilang' => $terbilang,
            'detailPayments' => $document->detailPayments
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

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'detailPayments' => $document->detailPayments
        ];

        $baseName = $this->sanitizeFileName($document->contract->contract_number . '_' . $document->contract->employee_name);
        $tempDir = storage_path('app/temp_' . uniqid());
        if (!file_exists($tempDir)) mkdir($tempDir, 0777, true);

        // Generate PDFs
        $letterPdfPath = $tempDir . "/Surat_{$baseName}.pdf";
        $invoicePdfPath = $tempDir . "/Invoice_{$baseName}.pdf";
        $kwitansiPdfPath = $tempDir . "/Kwitansi_{$baseName}.pdf";

        PDF::loadView('templates.document-letter', $data)->save($letterPdfPath);
        PDF::loadView('templates.document-invoice', $data)->save($invoicePdfPath);

        $firstCost = $document->accumulatedCosts->first();
        if (!$firstCost) return back()->with('error', 'Dokumen tidak memiliki akumulasi biaya.');

        $data['terbilang'] = $this->nilaiToString($firstCost->total);
        PDF::loadView('templates.document-kwitansi', $data)->save($kwitansiPdfPath);

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

        // if (!$firstCost) {
        //     return back()->with('error', 'Dokumen tidak memiliki akumulasi biaya.');
        // }

        // Hitung nilai terbilang dari total
        $terbilang = $firstCost ? $this->nilaiToString($firstCost->total) : '-';

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
| Non Management Fee PDF (Letter, Invoice, Kwitansi) BASE 64
|--------------------------------------------------------------------------
*/

    public function manfeeLetterBase64($document_id): string
    {
        $document = ManfeeDocument::with(['contract', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts
        ];

        $pdf = PDF::loadView('templates.management-fee.document-letter', $data);
        $pdfOutput = $pdf->output();
        $base64 = base64_encode($pdfOutput);

        return $base64;
    }

    public function manfeeInvoiceBase64($document_id): string
    {
        $document = ManfeeDocument::with(['contract', 'detailPayments', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'detailPayments' => $document->detailPayments
        ];

        $pdf = PDF::loadView('templates.management-fee.document-invoice', $data);
        $pdfOutput = $pdf->output();
        $base64 = base64_encode($pdfOutput);

        return $base64;
    }

    public function manfeeKwitansiBase64($document_id): string
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

        // Hitung nilai terbilang
        $terbilang = $this->nilaiToString($firstCost->total);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'terbilang' => $terbilang,
            'detailPayments' => $document->detailPayments
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

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
            'detailPayments' => $document->detailPayments
        ];

        $baseName = $this->sanitizeFileName($document->contract->contract_number . '_' . $document->contract->employee_name);
        $tempDir = storage_path('app/temp_' . uniqid());
        if (!file_exists($tempDir)) mkdir($tempDir, 0777, true);

        // Generate PDFs
        $letterPdfPath = $tempDir . "/Surat_{$baseName}.pdf";
        $invoicePdfPath = $tempDir . "/Invoice_{$baseName}.pdf";
        $kwitansiPdfPath = $tempDir . "/Kwitansi_{$baseName}.pdf";

        PDF::loadView('templates.document-letter', $data)->save($letterPdfPath);
        PDF::loadView('templates.document-invoice', $data)->save($invoicePdfPath);

        $firstCost = $document->accumulatedCosts->first();
        if (!$firstCost) return back()->with('error', 'Dokumen tidak memiliki akumulasi biaya.');

        $data['terbilang'] = $this->nilaiToString($firstCost->total);
        PDF::loadView('templates.document-kwitansi', $data)->save($kwitansiPdfPath);

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
| Convert to Base 64 for PrivyId
|--------------------------------------------------------------------------
*/

    //     public function nonManfeeLetterBase64($document_id)
    // {
    //     $document = NonManfeeDocument::with(['contract', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);

    //     $data = [
    //         'document' => $document,
    //         'contract' => $document->contract,
    //         'accumulatedCosts' => $document->accumulatedCosts,
    //     ];

    //     $pdf = Pdf::loadView('templates.document-letter', $data);

    //     // Ambil output binary dari PDF
    //     $pdfContent = $pdf->output();

    //     // Encode ke base64
    //     $base64Pdf = base64_encode($pdfContent);

    //     // Format sesuai requirement API
    //     $base64DataUrl = 'data:application/pdf;base64,' . $base64Pdf;

    //     return response()->json([
    //         'success' => true,
    //         'filename' => $document->letter_number . '.pdf',
    //         'document_base64' => $base64DataUrl, // ⬅️ inilah yang bisa kamu kirim ke API body
    //     ]);
    // }

    public function generateBase64Pdf(Request $request, $document_id)
    {
        $type = $request->query('type'); // contoh: 'non-manfee', 'manfee', 'invoice', 'kwitansi'
        $view = '';
        $document = null;
        $data = [];

        switch ($type) {
            case 'non-manfee-letter':
                $document = NonManfeeDocument::with(['contract', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);
                $view = 'templates.document-letter';
                $data = [
                    'document' => $document,
                    'contract' => $document->contract,
                    'accumulatedCosts' => $document->accumulatedCosts
                ];
                $filename = $this->sanitizeFileName($document->letter_number . '_' . $document->contract->contract_number . '_' . $document->contract->employee_name);
                break;

            case 'non-manfee-invoice':
                $document = NonManfeeDocument::with(['contract', 'detailPayments', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);
                $view = 'templates.document-invoice';
                $data = [
                    'document' => $document,
                    'contract' => $document->contract,
                    'accumulatedCosts' => $document->accumulatedCosts,
                    'detailPayments' => $document->detailPayments
                ];
                $filename = $this->sanitizeFileName($document->invoice_number . '_' . $document->contract->contract_number . '_' . $document->contract->employee_name);
                break;

            case 'non-manfee-kwitansi':
                $document = NonManfeeDocument::with(['contract', 'detailPayments', 'accumulatedCosts', 'bankAccount'])->findOrFail($document_id);
                $firstCost = $document->accumulatedCosts->first();
                if (!$firstCost) return response()->json(['success' => false, 'message' => 'Akumulasi biaya kosong.']);
                $view = 'templates.document-kwitansi';
                $data = [
                    'document' => $document,
                    'contract' => $document->contract,
                    'accumulatedCosts' => $document->accumulatedCosts,
                    'detailPayments' => $document->detailPayments,
                    'terbilang' => $this->nilaiToString($firstCost->total)
                ];
                $filename = $this->sanitizeFileName($document->receipt_number . '_' . $document->contract->contract_number . '_' . $document->contract->employee_name);
                break;

            case 'manfee-letter':
                $document = ManfeeDocument::with(['contract', 'accumulatedCosts'])->findOrFail($document_id);
                $view = 'templates.management-fee.document-letter';
                $data = [
                    'document' => $document,
                    'contract' => $document->contract,
                    'accumulatedCosts' => $document->accumulatedCosts
                ];
                $filename = $this->sanitizeFileName($document->letter_number . '_' . $document->contract->contract_number . '_' . $document->contract->employee_name);
                break;

            case 'manfee-invoice':
                $document = ManfeeDocument::with(['contract', 'detailPayments', 'accumulatedCosts'])->findOrFail($document_id);
                $view = 'templates.management-fee.document-invoice';
                $data = [
                    'document' => $document,
                    'contract' => $document->contract,
                    'accumulatedCosts' => $document->accumulatedCosts,
                    'detailPayments' => $document->detailPayments
                ];
                $filename = $this->sanitizeFileName($document->invoice_number . '_' . $document->contract->contract_number . '_' . $document->contract->employee_name);
                break;

            case 'manfee-kwitansi':
                $document = ManfeeDocument::with(['contract', 'detailPayments', 'accumulatedCosts'])->findOrFail($document_id);
                $firstCost = $document->accumulatedCosts->first();
                if (!$firstCost) return response()->json(['success' => false, 'message' => 'Akumulasi biaya kosong.']);
                $view = 'templates.management-fee.document-kwitansi';
                $data = [
                    'document' => $document,
                    'contract' => $document->contract,
                    'accumulatedCosts' => $document->accumulatedCosts,
                    'detailPayments' => $document->detailPayments,
                    'terbilang' => $this->nilaiToString($firstCost->total)
                ];
                $filename = $this->sanitizeFileName($document->receipt_number . '_' . $document->contract->contract_number . '_' . $document->contract->employee_name);
                break;

            default:
                return response()->json(['success' => false, 'message' => 'Jenis dokumen tidak valid.']);
        }

        // Generate PDF
        $pdf = Pdf::loadView($view, $data);
        $pdfContent = $pdf->output();
        $base64Pdf = base64_encode($pdfContent);
        $base64DataUrl = 'data:application/pdf;base64,' . $base64Pdf;

        return response()->json([
            'success' => true,
            'filename' => $filename . '.pdf',
            'document_base64' => $base64DataUrl,
        ]);
    }
}
