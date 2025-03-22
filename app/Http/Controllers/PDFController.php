<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\NonManfeeDocument;

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
    public function nonManfeeLetter($document_id)
    {
        $document = NonManfeeDocument::with(['contract', 'accumulatedCosts'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
        ];

        // format filename tersusun : letter_number/contract_number/nama_kontraktor 
        $filename = $data['document']->letter_number . '_' . $data['contract']->contract_number . '_' . $data['contract']->employee_name . '.pdf';

        // Load Blade view dari folder templates
        $pdf = Pdf::loadView('templates.document-letter', $data);

        // Download file PDF dengan nama document-letter.pdf
        // return $pdf->download('document-letter.pdf');
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
        $filename = $data['document']->invoice_number . '_' . $data['contract']->contract_number . '_' . $data['contract']->employee_name . '.pdf';

        // Load Blade view dari folder templates
        $pdf = Pdf::loadView('templates.document-invoice', $data);

        // Download file PDF dengan nama document-letter.pdf
        // return $pdf->download('document-invoice.pdf');
        return $pdf->stream($filename);
    }

    public function nonManfeeKwitansi($document_id)
    {
        $document = NonManfeeDocument::with(['contract', 'accumulatedCosts'])->findOrFail($document_id);

        $data = [
            'document' => $document,
            'contract' => $document->contract,
            'accumulatedCosts' => $document->accumulatedCosts,
        ];

        // format filename tersusun : receipt_number/contract_number/nama_kontraktor 
        $filename = $data['document']->receipt_number . '_' . $data['contract']->contract_number . '_' . $data['contract']->employee_name . '.pdf';

        // Load Blade view dari folder templates
        $pdf = Pdf::loadView('templates.document-kwitansi', $data);

        // Download file PDF dengan nama document-letter.pdf
        // return $pdf->download('document-kwitansi.pdf');
        return $pdf->stream($filename);
    }

    /*
|--------------------------------------------------------------------------
| Management Fee Function
|--------------------------------------------------------------------------
*/

    /** -.YOUR CODE.- */
}
