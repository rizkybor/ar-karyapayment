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
}
