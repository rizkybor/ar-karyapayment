<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    /**
     * Generate and download PDF file.
     *
     * @return \Illuminate\Http\Response
     */
    public function generatePDF()
    {
        $data = [
            'title' => 'Contoh PDF',
            'content' => 'Ini adalah contoh PDF dalam Laravel 10.'
        ];

        // Load Blade view dari folder templates
        $pdf = Pdf::loadView('templates.document-letter', $data);

        // Download file PDF dengan nama document-letter.pdf
        return $pdf->download('document-letter.pdf');
    }
}