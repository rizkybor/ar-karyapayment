<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class AccurateTransactionService
{
    private $client;
    private $apiSecret;
    private $accessToken;
    private $baseUrl;

    public function __construct()
    {
        // Initialize Guzzle Client
        $this->client = new Client();
        $this->apiSecret = config('services.accurate.api_secret');
        $this->accessToken = config('services.accurate.access_token');
        $this->baseUrl = config('services.accurate.base_url');
    }

    /**
     * Generate a signature for the request.
     */
    private function makeSignature($timestamp)
    {
        // Generate the HMAC SHA-256 hash
        $hashedSignature = hash_hmac('sha256', $timestamp, $this->apiSecret, true);
        // Base64 encode the hashed signature
        return base64_encode($hashedSignature);
    }

    /**
     * Get data from the external API.
     */
    public function getDataPenerimaan($page = 1, $pageSize = 10, $sort = 'transDate|desc')
    {
        if (!$this->accessToken) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature
        ];

        $url = $this->baseUrl . '/other-deposit/list.do?' . http_build_query([
            'fields' => 'id,number,transDate,bank,chequeNo,description,amount',
            'sp.page' => $page,
            'sp.pageSize' => $pageSize,
            'sp.sort' => $sort
        ]);

        $response = $this->client->get($url, ['headers' => $headers]);
        return $response->getBody()->getContents();
    }

    /**
     * Get data by Id from the external API. (di HIT untuk pengecekkan)
     */
    public function getDetailsPenerimaanView($idPayment)
    {
        if (!$this->accessToken) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature
        ];

        $url = $this->baseUrl . '/other-deposit/detail.do?id=' . $idPayment;
        $response = $this->client->get($url, ['headers' => $headers]);
        return $response->getBody()->getContents();
    }

    public function postDataInvoice($payload)
    {
        $tableData = $payload['detailPayments'];
        $tableTax = $payload['taxFiles'];

        /**
         * Example body request $detailAccounts.
         */

        // Assign Detail Account by Table Data
        $detailAccounts = [];
        foreach ($tableData as $data) {
            // angka hanya PPH yang berjenis Hutang Pajak
            // $amount = in_array($data['account'], ["210201", "210202", "210203", "210204", "210205", "210209"]) ? -abs($data['amount']) : $data['amount'];

            $detailAccounts[] = [
                "_status" => "insert",
                "itemId" => 207, // belum diketahui
                "detailName" => "Uang Muka Gaji TAD",
                "quantity" => 1, // belum ada
                "unitPrice" => 2000,
                "useTax1" => true, // belum ada
                "detailTaxName" => "PPN 10%", // belum ada
                "totalPrice" => 2000,
                "departmentId" => 151, // belum ada
                "projectId" => 457 // belum ada
            ];
        }

        // belum ada
        $detailTaxes = [];
        foreach ($tableTax as $data) {
            $detailTaxes[] = [
                "_status" => "insert",
                "taxId" => 50,
                "taxType" => "PPN",
                "taxDescription" => "Pajak Pertambahan Nilai",
                "taxableAmount" => 2000,
                "taxRate" => 11,
                "taxAmount" => 220,
                "new" => true
            ];
        }

        /**
         * Example body request.
         */

        $postData = [
            "uniqueDataNumber" => now()->timestamp, // atau bisa pakai uniqid()
            "needDetailResult" => false,
            "attachmentCount" => 0,
            "commentCount" => 0,
            "approvalDescription" => "",
            "rejectionReason" => "",
            "approvalIsUrgent" => false,
            "createdBy" => "",
            "createdById" => "",
            "id" => "",
            "optLock" => "",
            "forceCalculateTaxRate" => true,
            "forceCalculatePercentTaxable" => true,
            "number" => $payload['data']->invoice_number ?? '',
            "poNumber" => $payload['contract']->contract_number ?? '',
            "customerId" => 2200, // belum ada
            "currencyId" => 50,
            "paymentTermId" => 54,
            "toAddress" => $payload['contract']->address ?? '',
            "transDate" => now()->format('d/m/Y'),
            "description" => "DESCRIPTION",
            "tax1Id" => 50,
            "taxable" => true,
            "subTotal" => $payload['detailPayments'][0]->nilai_biaya ?? '',
            "tax1Amount" => 220,
            "totalAmount" => $payload['detailPayments'][0]->nilai_biaya ?? '',
            "detailItem" => $detailAccounts,
            "detailTax" => $detailTaxes,
            "retailWpName" => "Alfison",
            "documentCode" => "CTAS_INVOICE",
            "taxType" => "CTAS_KEPADA_SELAIN_PEMUNGUT_PPN",
            "taxDate" => now()->format('d/m/Y'),
            "saveAsStatusType" => "UNAPPROVED",
            "branchId" => 50
        ];

        // dd($postData,'<<< cek post data accurate');


        if (!$this->accessToken) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature,
            'Content-Type' => 'application/json'
        ];
        $url = $this->baseUrl . '/sales-invoice/save.do';
        try {
            $response = $this->client->post($url, [
                'headers' => $headers,
                'json' => $postData
            ]);

            if ($response->getStatusCode() === 200) {
                return $response;
            } else {
                throw new Exception('Unexpected status code: ' . $response->getStatusCode());
            }
        } catch (Exception $e) {
            throw new Exception('Failed to send request: ' . $e->getMessage());
        }
    }
}
