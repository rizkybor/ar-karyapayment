<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class AccurateTransactionService
{
    private $client;

    public function __construct()
    {
        // Initialize Guzzle Client
        $this->client = new Client();
    }

    /**
     * Generate a signature for the request.
     */
    private function makeSignature($timestamp)
    {
        // The Signature Secret key from your environment variables
        $secretKey = env('SIGNATURE_KEY'); // Make sure to set this in your .env file

        // Generate the HMAC SHA-256 hash
        $hashedSignature = hash_hmac('sha256', $timestamp, $secretKey, true);
        // Base64 encode the hashed signature
        return base64_encode($hashedSignature);
    }

    /**
     * Get data from the external API.
     */
    public function getDataPenerimaan($page = 1, $pageSize = 10, $sort = 'transDate|desc')
    {
        $token = env('ACCURATE_ACCESS_TOKEN');
        if (!$token) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature
        ];

        $url = 'https://zeus.accurate.id/accurate/api/other-deposit/list.do?' . http_build_query([
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
        $token = env('ACCURATE_ACCESS_TOKEN');
        if (!$token) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature
        ];

        $url = 'https://zeus.accurate.id/accurate/api/other-deposit/detail.do?id=' . $idPayment;
        $response = $this->client->get($url, ['headers' => $headers]);
        return $response->getBody()->getContents();
    }


    /**
     * Post data from the external API. (di HIT pada saat approval dirut.keuangan >> pajak)
     */
    public function postDataPenerimaan(array $postData)
    {

        /**
         * Example body request $detailAccounts.
         */

        // Assign Detail Account by Table Data
        //   $detailAccounts = [];
        //   foreach ($tableData as $data) {
        //        // angka hanya PPH yang berjenis Hutang Pajak
        //        $amount = in_array($data['account'], ["210201", "210202", "210203","210204", "210205", "210209"]) ? -abs($data['amount']) : $data['amount'];

        //       $detailAccounts[] = [
        //           'accountNo' => $data['account'],
        //           'amount' => $amount,
        //           'expenseName' => $data['account_name'],
        //           '_status' => 'insert',
        //           'dataClassification10Name' => '',
        //           'dataClassification1Name' => '',
        //           'dataClassification2Name' => '',
        //           'dataClassification3Name' => '',
        //           'dataClassification4Name' => '',
        //           'dataClassification5Name' => '',
        //           'dataClassification6Name' => '',
        //           'dataClassification7Name' => '',
        //           'dataClassification8Name' => '',
        //           'dataClassification9Name' => '',
        //           'departmentName' => '',
        //           'id' => '',
        //           'memo' => ''
        //       ];
        //   }

        /**
         * Example body request.
         */

        // $example = [
        //     'bankNo' => $bankNumberDatas['d']['no'],
        //     'detailAccount' => $detailAccounts,
        //     'payee' => $penerima,
        //     'transDate' => $formattedDate,
        //     'branchId' => '',
        //     'branchName' => '',
        //     'chequeDate' => '',
        //     'chequeNo' => $noCek,
        //     'description' => $catatan,
        //     'id' => '',
        //     'number' => $noBuktiFull,
        //     'rate' => '',
        //     'typeAutoNumber' => ''
        // ];

        $token = env('ACCURATE_ACCESS_TOKEN');
        if (!$token) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature,
            'Content-Type' => 'application/json'
        ];
        $url = 'https://zeus.accurate.id/accurate/api/other-deposit/save.do';
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

    public function postDataInvoice(array $postData, $tableData, $tableTax)
    {

        /**
         * Example body request $detailAccounts.
         */

        // Assign Detail Account by Table Data
        $detailAccounts = [];
        foreach ($tableData as $data) {
            // angka hanya PPH yang berjenis Hutang Pajak
            $amount = in_array($data['account'], ["210201", "210202", "210203", "210204", "210205", "210209"]) ? -abs($data['amount']) : $data['amount'];

            $detailAccounts[] = [
                "_status" => "insert",
                "itemId" => 207,
                "detailName" => "Uang Muka Gaji TAD",
                "quantity" => 1,
                "unitPrice" => 2000,
                "useTax1" => true,
                "detailTaxName" => "PPN 10%",
                "totalPrice" => 2000,
                "departmentId" => 151,
                "projectId" => 457
            ];
        }


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
            "number" => "NOMO INVOICE",
            "poNumber" => "",
            "customerId" => 2200,
            "currencyId" => 50,
            "paymentTermId" => 54,
            "toAddress" => "RAYA SERPONG PRIYANG Blok 000 No.000 RT:010 RW:008 Kel.PONDOK JAGUNG Kec.SERPONG\nUTARA Kota/Kab.TANGERANG SELATAN BANTEN 15326\nTangerang Selatan Tangerang 15326\nIndonesia",
            "transDate" => now()->format('d/m/Y'),
            "description" => "DESCRIPTION",
            "tax1Id" => 50,
            "taxable" => true,
            "subTotal" => 7000,
            "tax1Amount" => 220,
            "totalAmount" => 7220,
            "detailItem" => $detailAccounts,
            "detailTax" => $detailTaxes,
            "retailWpName" => "Alfison",
            "documentCode" => "CTAS_INVOICE",
            "taxType" => "CTAS_KEPADA_SELAIN_PEMUNGUT_PPN",
            "taxDate" => now()->format('d/m/Y'),
            "saveAsStatusType" => "UNAPPROVED",
            "branchId" => 50
        ];

        $token = env('ACCURATE_ACCESS_TOKEN');
        if (!$token) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature,
            'Content-Type' => 'application/json'
        ];
        $url = 'https://zeus.accurate.id/accurate/api/sales-invoice/save.do';
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
