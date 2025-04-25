<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class AccurateMasterOptionService
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

    // GET TRANSFER LIST 
    public function getBankTransferList()
    {
        if (!$this->accessToken) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        // Membuat timestamp saat ini
        $timestamp = now()->format('d/m/Y H:i:s');

        // Membuat signature
        $signature = $this->makeSignature($timestamp);

        // Header untuk request
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature
        ];

        // URL endpoint API Accurate
        $url = $this->baseUrl . '/glaccount/list.do';

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
        if (!$this->accessToken) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        // Membuat timestamp saat ini
        $timestamp = now()->format('d/m/Y H:i:s');

        // Membuat signature
        $signature = $this->makeSignature($timestamp);

        // Header untuk request
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature
        ];

        // URL endpoint API Accurate
        $url = $this->baseUrl . '/glaccount/list.do';

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
        if (!$this->accessToken) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        // Membuat timestamp saat ini
        $timestamp = now()->format('d/m/Y H:i:s');

        // Membuat signature
        $signature = $this->makeSignature($timestamp);

        // Header untuk request
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature
        ];

        // URL endpoint API Accurate
        $url = $this->baseUrl . '/glaccount/list.do';

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

    // GET INVENTORY LIST
    public function getInventoryList()
    {
        if (!$this->accessToken) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        // Membuat timestamp saat ini
        $timestamp = now()->format('d/m/Y H:i:s');

        // Membuat signature
        $signature = $this->makeSignature($timestamp);

        // Header untuk request
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature
        ];

        // URL endpoint API Accurate
        $url = $this->baseUrl . '/item/list.do';

        // Parameter query
        $queryParams = [
            'fields' => 'id,no,name,itemType',
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
        if (!$this->accessToken) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        // Membuat timestamp saat ini
        $timestamp = now()->format('d/m/Y H:i:s');

        // Membuat signature
        $signature = $this->makeSignature($timestamp);

        // Header untuk request
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature
        ];

        // URL endpoint API Accurate
        $url = $this->baseUrl . '/glaccount/detail.do';

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
