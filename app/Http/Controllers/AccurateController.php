<?php

namespace App\Http\Controllers;

use App\Services\AccurateTransactionService;
use Exception;

class AccurateController extends Controller
{
    public function deleteInvoice($id, AccurateTransactionService $accurate)
    {
        try {
            $result = $accurate->deleteSalesInvoice((int) $id);
            return response()->json(['success' => true, 'message' => 'Invoice berhasil dihapus dari Accurate.', 'result' => $result]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
