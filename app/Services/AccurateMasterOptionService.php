<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

        // Menggunakan cache untuk menyimpan hasil response
        $cacheKey = 'account_nonfee_list';

        $cacheDuration = 60; // dalam menit

        // Cek apakah data ada di cache
        $cachedResponse = Cache::get($cacheKey);
        if ($cachedResponse) {
            return $cachedResponse; // Kembalikan data dari cache
        }

        // Mengirim request GET ke API Accurate
        $response = $this->client->get($url, [
            'headers' => $headers,
            'query' => $queryParams
        ]);

        // Mengambil isi response dari request
        $responseData = $response->getBody()->getContents();

        // Simpan response ke cache
        Cache::put($cacheKey, $responseData, $cacheDuration);

        // Mengembalikan isi response
        return $responseData;
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

        // Data array kamu
        $accountTypes = [
            "INVENTORY",
            "NON_INVENTORY",
            "SERVICE",
            "GROUP"
        ];

        // Bikin query param
        $queryParams = [
            'fields' => 'id,no,name,accountType',
            'filter.leafOnly' => 'true',
            'field.accountType.op' => 'EQUAL',
            'sp.start' => 0,
            'sp.pageSize' => 100,
            'sp.sort' => 'name|asc'
        ];

        // Loop array buat masukin filter.accountType.val[n]
        foreach ($accountTypes as $index => $accountType) {
            $queryParams["filter.accountType.val[$index]"] = $accountType;
        }

        // Menggunakan cache untuk menyimpan hasil response
        $cacheKey = 'inventory_list';

        $cacheDuration = 60; // dalam menit

        // Cek apakah data ada di cache
        $cachedResponse = Cache::get($cacheKey);
        if ($cachedResponse) {
            return $cachedResponse; // Kembalikan data dari cache
        }

        // Mengirim request GET ke API Accurate
        $response = $this->client->get($url, [
            'headers' => $headers,
            'query' => $queryParams
        ]);

        // Mengambil isi response dari request
        $responseData = $response->getBody()->getContents();

        // Simpan response ke cache
        Cache::put($cacheKey, $responseData, $cacheDuration);

        // Mengembalikan isi response
        return $responseData;
    }

    public function testAccount()
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

        // Data array kamu
        $accountTypes = [
            "INVENTORY",
            "NON_INVENTORY",
            "SERVICE",
            "GROUP"
        ];

        // Bikin query param
        $queryParams = [
            'fields' => 'id,no,name,accountType',
            'filter.leafOnly' => 'true',
            'field.accountType.op' => 'EQUAL',
            'sp.start' => 0,
            'sp.pageSize' => 100,
            'sp.sort' => 'name|asc'
        ];

        // Loop array buat masukin filter.accountType.val[n]
        foreach ($accountTypes as $index => $accountType) {
            $queryParams["filter.accountType.val[$index]"] = $accountType;
        }

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

    public function getDataPenjualan($searchKeyword)
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

        $url = $this->baseUrl . '/sales-invoice/list.do';

        $queryParams = [
            'fields' => 'id,name,number,statusName',
            'sp.pageSize' => 100,
            'sp.start' => 0,
            'sp.limit' => 100,
        ];

        // Kalau user mau mencari berdasarkan number
        if ($searchKeyword) {
            $queryParams['filter.keywords.op'] = 'EQUAL';
            $queryParams['filter.keywords.val[0]'] = $searchKeyword;
        }

        // Menggunakan cache untuk menyimpan hasil response
        $cacheKey = 'data_penjualan';

        $cacheDuration = 60; // dalam menit

        // Cek apakah data ada di cache
        $cachedResponse = Cache::get($cacheKey);
        if ($cachedResponse) {
            return $cachedResponse; // Kembalikan data dari cache
        }

        $response = $this->client->get($url, [
            'headers' => $headers,
            'query' => $queryParams
        ]);

        // Mengambil isi response dari request
        $responseData = $response->getBody()->getContents();

        // Simpan response ke cache
        Cache::put($cacheKey, $responseData, $cacheDuration);

        // Mengembalikan isi response
        return $responseData;
    }

    public function getDepartmentList()
    {
        if (!$this->accessToken) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization'     => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp'   => $timestamp,
            'X-Api-Signature'   => $signature
        ];

        $url = $this->baseUrl . '/department/list.do';

        $queryParams = [
            'sp.page'                 => 1,
            'sp.pageSize'             => 20,
            'sp.sort'                 => 'name|asc'
        ];

        // Kirim request ke Accurate
        $response = $this->client->get($url, [
            'headers' => $headers,
            'query'   => $queryParams
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getDataClassificationList()
    {
        if (!$this->accessToken) {
            throw new Exception('Access Token atau Session ID belum diset.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization'     => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp'   => $timestamp,
            'X-Api-Signature'   => $signature
        ];

        $queryParams = [
            'sp.page'                 => 1,
            'sp.pageSize'             => 20,
            'sp.sort'                 => 'name|asc'
        ];

        $url = $this->baseUrl . '/data-classification/list.do';

        $response = $this->client->get($url, [
            'headers' => $headers,
            'query'   => $queryParams,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getProjectList()
    {
        if (!$this->accessToken) {
            throw new Exception('Access Token atau Session ID belum diset.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization'     => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp'   => $timestamp,
            'X-Api-Signature'   => $signature
        ];

        $allProjects = [];
        $page = 1;
        $pageSize = 20;
        $totalPages = 1; // default awal

        do {
            $queryParams = [
                'sp.page'     => $page,
                'sp.pageSize' => $pageSize,
            ];

            $url = $this->baseUrl . '/project/list.do?' . http_build_query($queryParams);

            $response = $this->client->get($url, [
                'headers' => $headers,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['d']) && is_array($data['d'])) {
                $allProjects = array_merge($allProjects, $data['d']);
            }

            $totalPages = $data['sp']['pageCount'] ?? 1;
            $page++;
        } while ($page <= $totalPages);
        return $allProjects;
    }
}
