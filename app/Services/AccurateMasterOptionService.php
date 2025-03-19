<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class AccurateMasterOptionService
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
        $secretKey = env('ACCURATE_SIGNATURE_SECRET'); // Make sure to set this in your .env file

        // Generate the HMAC SHA-256 hash
        $hashedSignature = hash_hmac('sha256', $timestamp, $secretKey, true);
        // Base64 encode the hashed signatureSIGNATURE_KEY
        return base64_encode($hashedSignature);
    }

    // GET TRANSFER LIST 
    public function getBankTransferList()
    {
        // Mendapatkan token dari file .env
        $token = env('ACCURATE_ACCESS_TOKEN');
        if (!$token) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        // Membuat timestamp saat ini
        $timestamp = now()->format('d/m/Y H:i:s');

        // Membuat signature
        $signature = $this->makeSignature($timestamp);

        // Header untuk request
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature
        ];

        // URL endpoint API Accurate
        $url = 'https://zeus.accurate.id/accurate/api/glaccount/list.do';

        // Parameter query
        $queryParams = [
            'fields' => 'id,no,name,accountType',
            'filter.leafOnly' => 'true',
            'field.accountType.op' => 'EQUAL',
            'filter.accountType.val[0]' => 'CASH_BANK'
        ];

        // Mengirim request GET ke API Accurate
        $response = $this->client->get($url, [
            'headers' => $headers,
            'query' => $queryParams
        ]);


        // Mengembalikan isi response dari request
        return $response->getBody()->getContents();
    }

    // GET ASSETS LIST
    public function getAssetList()
    {
        // Mendapatkan token dari file .env
        $token = env('ACCURATE_ACCESS_TOKEN');
        if (!$token) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        // Membuat timestamp saat ini
        $timestamp = now()->format('d/m/Y H:i:s');

        // Membuat signature
        $signature = $this->makeSignature($timestamp);

        // Header untuk request
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature
        ];

        // URL endpoint API Accurate
        $url = 'https://zeus.accurate.id/accurate/api/glaccount/list.do';

        // Parameter query
        $queryParams = [
            'fields' => 'id,no,name,accountType',
            'filter.leafOnly' => 'true',
            'field.accountType.op' => 'EQUAL',
            'filter.accountType.val[0]' => 'OTHER_CURRENT_ASSET',
        ];

        // Mengirim request GET ke API Accurate
        $response = $this->client->get($url, [
            'headers' => $headers,
            'query' => $queryParams
        ]);

        // Mengembalikan isi response dari request
        return $response->getBody()->getContents();
    }

    // GET ACCOUNT NON FEE LIST
    public function getAccountNonFeeList()
    {
        // Mendapatkan token dari file .env
        $token = env('ACCURATE_ACCESS_TOKEN');
        if (!$token) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        // Membuat timestamp saat ini
        $timestamp = now()->format('d/m/Y H:i:s');

        // Membuat signature
        $signature = $this->makeSignature($timestamp);

        // Header untuk request
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature
        ];

        // URL endpoint API Accurate
        $url = 'https://zeus.accurate.id/accurate/api/glaccount/list.do';

        // Parameter query
        $queryParams = [
            'fields' => 'id,no,name,accountType',
            'filter.leafOnly' => 'true',
            'field.accountType.op' => 'EQUAL',
            'filter.accountType.val[0]' => 'COGS',
        ];

        // Mengirim request GET ke API Accurate
        $response = $this->client->get($url, [
            'headers' => $headers,
            'query' => $queryParams
        ]);

        // Mengembalikan isi response dari request
        return $response->getBody()->getContents();
    }

    // GET BANK DETAILS 
    public function bankDetails($bankId)
    {
        // Mendapatkan token dari file .env
        $token = env('ACCURATE_ACCESS_TOKEN');
        if (!$token) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        // Membuat timestamp saat ini
        $timestamp = now()->format('d/m/Y H:i:s');

        // Membuat signature
        $signature = $this->makeSignature($timestamp);

        // Header untuk request
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature
        ];

        // URL endpoint API Accurate
        $url = 'https://zeus.accurate.id/accurate/api/glaccount/detail.do';

        // Parameter query
        $queryParams = [
            'id' => $bankId,
        ];

        // Mengirim request GET ke API Accurate
        $response = $this->client->get($url, [
            'headers' => $headers,
            'query' => $queryParams
        ]);
        return $response->getBody()->getContents();
    }
}
