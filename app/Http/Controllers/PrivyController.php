<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\FilePrivy;
use App\Services\PrivyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PrivyController extends Controller
{
    // Payload Privy Pattern 
    public function buildPrivyPayload($base64Pdf, $typeSign, $posX, $posY, $ref, $no, $jenis_dokumen)
    {
        // Generate reference_number: REFYYYYMMDDprimeXXXX
        $today = Carbon::now();
        $referenceNumber = $ref;

        $payload = [
            "reference_number" => $referenceNumber,
            "channel_id" => env('PRIVY_CHANNEL_ID'),
            "custom_signature_placement" => true,
            "doc_process" => $typeSign,
            "visibility" => true,
            "doc_owner" => [
                "privyId" => env('PRIVY_ID'),
                "enterpriseToken" => env('PRIVY_ENTERPRISE_TOKEN'),
            ],
            "document" => [
                "document_file" => "data:application/pdf;base64," . $base64Pdf,
                "document_name" => "PRIVY_" . $no,
                "sign_process" => "1",
                "barcode_position" => "1",
            ],
            "recipients" => [
                [
                    "detail" => "0",
                    "user_type" => "0",
                    "autosign" => "1",
                    "id_user" => env('PRIVY_ID_USER'),
                    "signer_type" => "Signer",
                    "enterpriseToken" => env('PRIVY_ENTERPRISE_TOKEN'),
                    "sign_positions" => [
                        [
                            "posX" => $posX,
                            "posY" => $posY,
                            "signPage" => "1",
                        ],
                    ]
                ]
            ]
        ];

        if ($typeSign == 2) {
            $payload['e_meterai'] = [
                "doc_category" => "Surat Perjanjian",
                "stamp_position" => [
                    [
                        "dimension" => 100,
                        "pos_x" => $jenis_dokumen == 'management_fee' ? 424.58 : 424.58,
                        "pos_y" => $jenis_dokumen == 'management_fee' ? 820.00 : 820.00,
                        "page" => 1
                    ]
                ]
            ];
        }

        return $payload;
    }

    public function generateDocument(Request $request, $privy)
    {
        try {
            // Validasi input minimal
            $request->validate([
                'base64_pdf' => 'required|string',
                'type_sign' => 'required|in:0,1,2',
                'posX' => 'required|string',
                'posY' => 'required|string',
                'docType' => 'required|string',
                'noSurat' => 'required|string',
                'jenis_dokumen' => 'nullable',
            ]);
        } catch (ValidationException $e) {
            return [
                'status' => 'ERROR',
                'message' => 'Validasi gagal',
                'error' => $e->errors(),
            ];
        }

        // Ambil input
        $base64Pdf = $request->base64_pdf;
        $typeSign = $request->type_sign;
        $posX = $request->posX;
        $posY = $request->posY;
        $ref = $request->docType;
        $no = $request->noSurat;
        $jenis_dokumen = $request->jenis_dokumen ?? null;

        // Bangun payload secara otomatis
        $payload = $this->buildPrivyPayload($base64Pdf,  $typeSign, $posX, $posY, $ref, $no, $jenis_dokumen);


        Log::info('Privy uploadDocument response:', [
            'type' => $typeSign,
            'payload' => $payload,
            'jenis_dokumen' => $jenis_dokumen,
        ]);

        try {
            // Kirim ke Privy
            $response = $privy->uploadDocument($payload);

            Log::info('Privy uploadDocument response:', [
                'no_surat' => $no,
                'reference' => $ref,
                'response' => $response,
            ]);

            return $response;
        } catch (\Throwable $e) {
            return [
                'status' => 'ERROR',
                'message' => 'Gagal upload dokumen ke Privy',
                'error' => $e->getMessage(),
            ];
        }
    }

    // fungsi untuk print single 
    public function checkDocumentStatus(Request $request, PrivyService $privy)
    {
        $documentId = $request->input('document_id');
        $category_type = $request->input('category_type');
        $type = $request->input('type_document', 'letter');

        if (!in_array($type, ['letter', 'invoice', 'kwitansi'])) {
            return response()->json([
                'error' => [
                    'code' => 422,
                    'message' => 'Tipe dokumen tidak valid. Gunakan letter, invoice, atau kwitansi.'
                ]
            ], 422);
        }

        $filePrivy = FilePrivy::where('document_id', $documentId)
            ->where('category_type', $category_type)
            ->where('type_document', $type)
            ->first();

        if (!$filePrivy) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "FilePrivy untuk type '{$type}' tidak ditemukan."
                ]
            ], 404);
        }

        $payload = [
            'reference_number' => $filePrivy->reference_number,
            'channel_id'       => $filePrivy->channel_id ?? 'default_channel',
            'document_token'   => $filePrivy->document_token,
        ];

        $response = $privy->checkDocSigningStatus($payload);

        Log::info('Privy check file response:', [
            'status' => 201,
            'response' => $response,
        ]);
        return response()->json($response);
    }

    // fungsi untuk export all
    public function getSignedDocumentUrl($documentId, $category_type, $type)
    {
        if (!in_array($type, ['letter', 'invoice', 'kwitansi'])) {
            return null;
        }

        $filePrivy = FilePrivy::where('document_id', $documentId)
            ->where('category_type', $category_type)
            ->where('type_document', $type)
            ->first();

        if (!$filePrivy) {
            return null;
        }

        $payload = [
            'reference_number' => $filePrivy->reference_number,
            'channel_id'       => $filePrivy->channel_id ?? 'default_channel',
            'document_token'   => $filePrivy->document_token,
        ];

        $privy = new PrivyService();
        $response = $privy->checkDocSigningStatus($payload);

        return $response['data']['signed_document'] ?? null;
    }
}
