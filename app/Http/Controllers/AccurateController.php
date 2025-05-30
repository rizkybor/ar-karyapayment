<?php

namespace App\Http\Controllers;

use App\Services\AccurateTransactionService;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\Request;

class AccurateController extends Controller
{
    public function deleteInvoice(Request $request, AccurateTransactionService $accurate)
    {
        try {
            $id_accurate = $request->input('number');
            $result = $accurate->deleteSalesInvoice($id_accurate);

            return response()->json(['success' => true, 'message' => 'Invoice berhasil dihapus dari Accurate.', 'result' => $result]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
