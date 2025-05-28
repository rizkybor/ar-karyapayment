<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
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

        // Assign Detail Account by Table Data
        $detailAccounts = [];
        foreach ($tableData as $data) {
            $detailAccounts[] = [
                "_status" => "insert",
                "id" => "",
                "idId" => "",
                "seq" => 1,
                "groupSeq" => "",
                "groupSeqId" => "",
                "inputGroupSeq" => "",
                "inputGroupSeqId" => "",
                "itemId" => $data->item_detail['id'] ?? '',
                "detailName" => $payload['data']->category == "management_fee"
                    ? ($data['expense_type'] ?? '')
                    : ($data['account_name'] ?? ''),
                "quantity" => 1,
                "controlQuantity" => 0,
                "" => 100,
                "uniitemUnitIdtRatio" => 1,
                "unitPrice" => (int) $data['nilai_biaya'] ?? '',
                "canChangeDetailGroup" => false,
                "isFromMemorize" => false,
                "dynamicGroup" => false,
                "detailDynamicGroup" => false,
                "detailDynamicGroupPrice" => 0,
                "detailDynamicGroupTotalPrice" => 0,
                "itemDiscPercent" => "",
                "itemCashDiscount" => 0,
                "lastItemDiscPercent" => "",
                "lastItemCashDiscount" => 0,
                "useTax1" => $data['expense_type'] == "Biaya Personil" ? false : true,
                "useTax2" => false,
                "useTax3" => $data->item_detail['tax3Id'] ? true : false,
                "useTax4" => false,
                "tax3Id" => $data->item_detail['tax3Id'] ?? '',
                "tax3" => $data->item_detail['tax3'] ?? '',
                "taxableAmount3" => 0,
                "detailTaxName" => $data->item_detail['tax3Id'] ? "PPN 10%, PPh 23 2%" : "PPN 10%",
                "totalPrice" => (int) $data['nilai_biaya'] ?? '',
                "department" => "",
                "departmentId" => "",
                "project" => "",
                "projectId" => "",
                "salesmanListId" => 0,
                "salesmanName" => "",
                "posSalesType" => "",
                "posSalesTypeId" => "",
                "hasPosSalesType" => false,
                "warehouse" => "",
                "warehouseId" => "",
                "availableStock" => 0,
                "availableStockUnit" => 0,
                "warehouseStock" => 0,
                "warehouseStockUnit" => 0,
                "totalCaptionSerialNumber" => "0+No+Seri/Produksi.+Isikan+1+lagi",
                "salesQuotationDetail" => "",
                "salesQuotationDetailId" => "",
                "salesQuotation" => "",
                "salesQuotationId" => "",
                "salesOrderDetail" => "",
                "salesOrderDetailId" => "",
                "salesOrder" => "",
                "salesOrderId" => "",
                "deliveryOrderDetail" => "",
                "deliveryOrderDetailId" => "",
                "deliveryOrder" => "",
                "deliveryOrderId" => "",
                "purchaseInvoiceDetail" => "",
                "purchaseInvoiceDetailId" => "",
                "purchaseInvoice" => "",
                "purchaseInvoiceId" => "",
                "itemDiscountAccountId" => "",
                "itemDiscountAccountIdId" => "",
                "amortizeAccount" => "",
                "amortizeAccountId" => "",
                "amortizeMonth" => 0,
                "startAmortizeMonth" => 5,
                "startAmortizeYear" => 2025,
                "startAmortizeType" => "SET",
                "hasAmortize" => false,
                "detailNotes" => "",
                "detailSerialNumberId" => 0,
                "deliveredQuantity" => 0,
                "processQuantityDesc" => "",
                "salesOrderPoNumber" => "",
                "deliveryOrderPoNumber" => "",
                "optionDynamicGroupId" => 0,
                "charField1" => $data->item_detail['charField1'] ?? '',
                "charField2" => $data->item_detail['charField2'] ?? '',
                "charField3" => $data->item_detail['charField3'] ?? '',
                "charField4" => $data->item_detail['charField4'] ?? '',
                "charField5" => $data->item_detail['charField5'] ?? '',
                "charField6" => $data->item_detail['charField6'] ?? '',
                "charField7" => $data->item_detail['charField7'] ?? '',
                "charField8" => $data->item_detail['charField8'] ?? '',
                "charField9" => $data->item_detail['charField9'] ?? '',
                "charField10" => "",
                "charField11" => "",
                "charField12" => "",
                "charField13" => "",
                "charField14" => "",
                "charField15" => "",
                "numericField1" => 0,
                "numericField2" => 0,
                "numericField3" => 0,
                "numericField4" => 0,
                "numericField5" => 0,
                "numericField6" => 0,
                "numericField7" => 0,
                "numericField8" => 0,
                "numericField9" => 0,
                "numericField10" => $data->item_detail['numericField10'] ?? '',
                "dateField1" => $data->item_detail['dateField1'] ?? '',
                "dateField2" => "",
                "dataClassification1Id" => "",
                "dataClassification2Id" => "",
                "dataClassification3Id" => "",
                "dataClassification4Id" => "",
                "dataClassification5Id" => "",
                "dataClassification6Id" => "",
                "dataClassification7Id" => "",
                "dataClassification8Id" => "",
                "dataClassification9Id" => "",
                "dataClassification10Id" => "",
                "oldPriceMinQty" => 0,
                "oldDiscountMinQty" => 0,
                "isLoading" => false,
                "customerId" => $data['customer']['id'] ?? ''
            ];
        }

        if ($payload['data']->category == "management_fee") {
            // KHUSUS MANFEE (NAMBAH MANFEE ACCOUNT & TAX)
            $manfee = $payload['accumulatedCosts']->first();

            $detailAccounts[] = [
                "_status" => "insert",
                "id" => "",
                "idId" => "",
                "seq" => count($detailAccounts) + 1,
                "groupSeq" => "",
                "groupSeqId" => "",
                "inputGroupSeq" => "",
                "inputGroupSeqId" => "",
                "itemId" => $manfee['accountId'] ?? null,
                "detailName" => $manfee['account_name'] ?? 'Management Fee',
                "quantity" => 1,
                "controlQuantity" => 0,
                "" => 100,
                "uniitemUnitIdtRatio" => 1,
                "unitPrice" => (int) $manfee['nilai_manfee'] ?? 0,
                "canChangeDetailGroup" => false,
                "isFromMemorize" => false,
                "dynamicGroup" => false,
                "detailDynamicGroup" => false,
                "detailDynamicGroupPrice" => 0,
                "detailDynamicGroupTotalPrice" => 0,
                "itemDiscPercent" => "",
                "itemCashDiscount" => 0,
                "lastItemDiscPercent" => "",
                "lastItemCashDiscount" => 0,
                "useTax1" => true,
                "tax1Id" => (int) $manfee['accountId'],
                "detailTaxName" => "PPN {$manfee['rate_ppn']}%",
                "totalPrice" => (int) $manfee['nilai_manfee'],
                "numericField10" => 0,
                "dateField1" => "",
                "customerId" => $payload['customer']['id'] ?? ''
            ];

            $detailTaxes = [];
            foreach ($tableTax as $data) {
                $detailTaxes[] = [
                    "id" => "",
                    "idId" => "",
                    "seq" => "",
                    "seqId" => "",
                    "_status" => "insert",
                    "taxId" => 50,
                    "taxType" => "PPN",
                    "pph23Type" => "",
                    "pph23TypeId" => "",
                    "pph15Type" => "",
                    "pph15TypeId" => "",
                    "pphPs4Type" => "",
                    "pphPs4TypeId" => "",
                    "taxDescription" => "Pajak Pertambahan Nilai",
                    "taxableAmount" => 2000,
                    "taxRate" => (int)$payload['accumulatedCosts']->first()->rate_ppn,
                    "taxAmount" => 220,
                    "remove" => false,
                    "detailInvoiceId" => "",
                    "detailInvoiceIdId" => "",
                    "detailInvoiceNo" => "",
                    "detailInvoiceNoId" => "",
                    "purchaseDetail" => false,
                    "new" => true
                ];
            }
        } else {
            // NON MANAGEMENT FEE TAX 
            $detailTaxes = [
                [
                    "id" => "",
                    "idId" => "",
                    "seq" => "",
                    "seqId" => "",
                    "_status" => "insert",
                    "taxId" => 57,
                    "taxType" => "PPH23",
                    "pph23Type" => "JASA_MANAJEMEN",
                    "pph23TypeId" => "",
                    "pph15Type" => "",
                    "pph15TypeId" => "",
                    "pphPs4Type" => "",
                    "pphPs4TypeId" => "",
                    "taxDescription" => "Jasa Manajemen",
                    "taxableAmount" => 100000,
                    "taxRate" => 2,
                    "taxAmount" => 2000,
                    "remove" => false,
                    "detailInvoiceId" => "",
                    "detailInvoiceIdId" => "",
                    "detailInvoiceNo" => "",
                    "detailInvoiceNoId" => "",
                    "purchaseDetail" => false,
                    "new" => true
                ],
                [
                    "id" => "",
                    "idId" => "",
                    "seq" => "",
                    "seqId" => "",
                    "_status" => "insert",
                    "taxId" => 50,
                    "taxType" => "PPN",
                    "pph23Type" => "",
                    "pph23TypeId" => "",
                    "pph15Type" => "",
                    "pph15TypeId" => "",
                    "pphPs4Type" => "",
                    "pphPs4TypeId" => "",
                    "taxDescription" => "Pajak Pertambahan Nilai",
                    "taxableAmount" => 400000,
                    "taxRate" => 11,
                    "taxAmount" => 44000,
                    "remove" => false,
                    "detailInvoiceId" => "",
                    "detailInvoiceIdId" => "",
                    "detailInvoiceNo" => "",
                    "detailInvoiceNoId" => "",
                    "purchaseDetail" => false,
                    "new" => true
                ]
            ];
        }

        // dd($detailAccounts);

        /**
         * Example body request.
         */
        $postData = [
            "uniqueDataNumber" => now()->timestamp,
            "needDetailResult" => false,
            "attachmentCount" => 0,
            "commentCount" => 0,
            "approvalDescription" => "",
            "rejectionReason" => "",
            "approvalIsUrgent" => false,
            "createdBy" => "",
            "createdById" => "",
            "id" => "",
            "idId" => "",
            "optLock" => "",
            "optLockId" => "",
            "forceCalculateTaxRate" => true,
            "forceCalculatePercentTaxable" => true,
            "number" => $payload['data']->invoice_number ?? '',
            "salesOrderPoNumber" => "",
            "deliveryOrderPoNumber" => "",
            "countAutoNumber" => 1,
            "recurringDetailId" => "",
            "recurringDetailIdId" => "",
            "poNumber" => $payload['contract']->contract_number ?? '',
            "customerId" => $payload['customer']['id'] ?? '',
            "customerName" => "",
            "customerNameId" => "",
            "currencyId" => $payload['customer']['currency']['id'] ?? '',
            "masterSalesmanName" => "",
            "cashierEmployeeName" => "",
            "masterPosSalesType" => "",
            "masterPosSalesTypeId" => "",
            "arAccount" => "",
            "arAccountId" => "",
            "rate" => 1,
            "fiscalRate" => 1,
            "fob" => "",
            "fobId" => "",
            "paymentTermId" => $payload['customer']['term']['id'] ?? '',
            "shipment" => "",
            "shipmentId" => "",
            "toAddress" => $payload['contract']->address ?? '',
            "transDate" => now()->format('d/m/Y'),
            "shipDate" => now()->format('d/m/Y'),
            "dueDate" => now()->format('d/m/Y'),
            "description" => $payload['data']->letter_subject ?? NULL,
            "tax1Id" => 50,
            "tax2Id" => "",
            "tax2IdId" => "",
            "tax4Id" => "",
            "tax4IdId" => "",
            "tax2" => "",
            "tax4" => "",
            "taxable" => true,
            "inclusiveTax" => false,
            "multiTaxRate" => false,
            "subTotal" =>  round($payload['detailPayments'][0]->nilai_biaya) ?? '',
            "cashDiscPercent" => "",
            "cashDiscount" => 0,
            "lastCashDiscPercent" => "",
            "lastCashDiscount" => 0,
            "tax1Amount" => 220,
            "tax2Amount" => 0,
            "tax1AmountBase" => 33000,
            "tax2AmountBase" => 0,
            "tax4AmountBase" => 0,
            "tax3Amount" => 2000,
            "tax4Amount" => 0,
            "tax1Rate" => (int)$payload['accumulatedCosts']->first()->rate_ppn,
            "tax2Rate" => 0,
            "tax4Rate" => 0,
            "percentTaxable" => 100,
            "totalExpense" => 0,
            "totalAmount" =>  round($payload['detailPayments'][0]->nilai_biaya) ?? '',
            "detailItem" => $detailAccounts,
            "detailExpenseId" => 0,
            "detailDownPaymentId" => 0,
            "detailSchedulePaymentId" => 0,
            "journalId" => "",
            "journalIdId" => "",
            "transactionCurrencyId" => "",
            "transactionCurrencyIdId" => "",
            "openingBalance" => false,
            "reverseInvoice" => false,
            "comment" => "",
            "attachments" => "[]",
            "canSelfPaidPph" => false,
            "detailTax" => $detailTaxes,
            "primeReceipt" => 0,
            "taxReceipt" => 0,
            "primeReturn" => 0,
            "taxReturn" => 0,
            "primeOwing" => 0,
            "taxOwing" => 0,
            "suggestedDiscount" => 0,
            "status" => "",
            "statusName" => "",
            "hasStatusHistory" => false,
            "retailWpName" => $payload['customer']['wpName'] ?? '',
            "retailIdCard" => "",
            "invoiceDiscountAccountId" => "",
            "invoiceDiscountAccountIdId" => "",
            "invoiceDp" => false,
            "inputDownPayment" => 0,
            "orderDownPayment" => "",
            "orderDownPaymentId" => "",
            "outstanding" => true,
            "owingForPayment" => 0,
            "customerHasDownPayment" => false,
            "userPrinted" => "",
            "userPrintedId" => "",
            "printUserName" => "",
            "printedTime" => "",
            "customerHasProjectContractor" => false,
            "projectContractor" => "",
            "projectContractorId" => "",
            "projectAmount" => 0,
            "projectAddendum" => 0,
            "projectProgress" => "",
            "projectProgressId" => "",
            "projectPaymentPortion" => "",
            "projectPaymentPortionId" => "",
            "projectPaymentAmount" => 0,
            "taxDate" => now()->format('d/m/Y'),
            "taxNumber" => "",
            "documentCode" => "CTAS_INVOICE",
            "taxType" => 'BKN_PEMUNGUT_PPN',
            "documentTransaction" => "",
            "documentTransactionId" => "",
            "notesIdTax" => "",
            "notesIdTaxId" => "",
            "taxNumberType" => "MAIN_NUMBER",
            "reversePercentShipped" => 0,
            "reversePercentShippedPoint" => "0%",
            "charField1" => "",
            "charField2" => "",
            "charField3" => "",
            "charField4" => "",
            "charField5" => "",
            "charField6" => "",
            "charField7" => "",
            "charField8" => "",
            "charField9" => "",
            "charField10" => "",
            "numericField1" => 0,
            "numericField2" => 0,
            "numericField3" => 0,
            "numericField4" => 0,
            "numericField5" => 0,
            "numericField6" => 0,
            "numericField7" => 0,
            "numericField8" => 0,
            "numericField9" => 0,
            "numericField10" => 0,
            "dateField1" => "",
            "dateField2" => "",
            "approvalDetailListId" => 0,
            "approvalStatus" => "",
            "saveAsStatusType" => "UNAPPROVED",
            "efakturPdfFilePath" => "",
            "uniqueAmount" => 0,
            "paymentWithUniqueAmount" => false,
            "paymentWithVirtualAccount" => false,
            "paymentTermIsInstallment" => false,
            "totalAmountWithUniqueAmount" => 0,
            "taxAutoNumber" => true,
            "branchId" => $payload['customer']['branchId'] ?? 50,
            "receiptHistoryId" => 0,
            "taxReceiptHistoryId" => 0,
            "returnHistoryId" => 0,
            "deliveryOrderHistoryId" => 0,
            "exchangeInvoiceId" => "",
            "exchangeInvoiceIdId" => "",
            "deliveryPackingId" => "",
            "deliveryPackingIdId" => "",
            "exchangeInvoiceNumber" => "",
            "deliveryPackingNumber" => "",
            "commentsId" => 0,
            "vaNumber" => "",
            "vaNumberObject" => "",
            "vaNumberObjectId" => "",
            "hasVaCompanyCode" => false,
            "epaymentCode" => "",
            "epaymentType" => "",
            "externalId" => "",
            "subComp" => "",
            "subCompObject" => "",
            "subCompObjectId" => "",
            "vaNumberListId" => 0,
            "defaultVA" => "",
            "epaymentAmount" => 0,
            "epaymentStatus" => "",
            "epaymentStatusName" => "",
            "paymentName" => "",
            "posCreateDate" => "",
            "settingId" => 0,
            "layoutDocId" => 0,
            "expiredDate" => "",
            "epaymentSetting" => "",
            "epaymentSettingId" => "",
            "ignoreWarning" => false
        ];

        // dd($postData['detailItem']);
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
                $responseBody = json_decode((string) $response->getBody(), true);

                $success = $responseBody['s'] ?? null;
                $messages = $responseBody['d'] ?? [];

                // Log lengkap untuk audit trail
                Log::info('Accurate Sales Invoice Response', [
                    'timestamp' => now()->toDateTimeString(),
                    'url' => $url,
                    'headers' => $headers,
                    // 'payload' => $postData,
                    'response' => $responseBody,
                    'success_flag' => $success,
                    'messages' => $messages,
                ]);

                // Jika gagal (s === false)
                if ($success === false) {
                    throw new Exception('Gagal simpan invoice: ' . implode('; ', $messages));
                }

                return $responseBody;
            } else {
                Log::error('Accurate Invoice Unexpected Status Code', [
                    'status_code' => $response->getStatusCode(),
                    'body' => (string) $response->getBody(),
                ]);
                throw new Exception('Unexpected status code: ' . $response->getStatusCode());
            }
        } catch (Exception $e) {
            throw new Exception('Failed to send request: ' . $e->getMessage());
        }
    }


    public function getAllCustomers(array $filters = [], int $pageSize = 100)
    {
        if (!$this->accessToken) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature,
        ];

        $allCustomers = [];
        $page = 1;

        do {
            // gabungkan filter + pagination
            $query = array_merge([
                'fields' => 'id,name,customerNo,npwpNo',
                'sp.page' => $page,
                'sp.pageSize' => $pageSize
            ], $filters);

            $url = $this->baseUrl . '/customer/list.do?' . http_build_query($query);

            try {
                $response = $this->client->get($url, ['headers' => $headers]);
                $result = json_decode($response->getBody()->getContents(), true);

                if (!isset($result['d'])) {
                    break;
                }

                $allCustomers = array_merge($allCustomers, $result['d']);

                $totalPages = $result['sp']['totalPages'] ?? 1;
                $page++;
            } catch (Exception $e) {
                throw new Exception("Failed on page {$page}: " . $e->getMessage());
            }
        } while ($page <= $totalPages);

        return $allCustomers;
    }

    public function getCustomerDetail(array $params)
    {
        if (!$this->accessToken) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature,
        ];

        // Minimal harus ada salah satu dari 'id' atau 'customerNo'
        if (!isset($params['id']) && !isset($params['customerNo'])) {
            throw new Exception("Parameter 'id' atau 'customerNo' wajib diisi.");
        }

        $url = $this->baseUrl . '/customer/detail.do?' . http_build_query($params);

        try {
            $response = $this->client->get($url, ['headers' => $headers]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to get customer detail: ' . $e->getMessage());
        }
    }


    public function saveCustomer(string $name)
    {
        if (!$this->accessToken) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature,
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $formParams = [
            'name' => $name
        ];

        $url = $this->baseUrl . '/customer/save.do';

        try {
            $response = $this->client->post($url, [
                'headers' => $headers,
                'form_params' => $formParams
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            } else {
                throw new Exception('Unexpected status code: ' . $response->getStatusCode());
            }
        } catch (Exception $e) {
            throw new Exception('Failed to save customer: ' . $e->getMessage());
        }
    }

    public function getItemDetail(array $params)
    {
        if (!$this->accessToken) {
            throw new Exception('ACCURATE_ACCESS_TOKEN is not set.');
        }

        $timestamp = now()->format('d/m/Y H:i:s');
        $signature = $this->makeSignature($timestamp);

        $headers = [
            'Authorization'     => 'Bearer ' . $this->accessToken,
            'X-Api-Timestamp'   => $timestamp,
            'X-Api-Signature'   => $signature,
        ];

        // Minimal harus ada salah satu dari 'id' atau 'no'
        if (!isset($params['id']) && !isset($params['no'])) {
            throw new Exception("Parameter 'id' atau 'no' wajib diisi.");
        }

        // Siapkan query parameter hanya untuk 'id' dan 'no'
        $queryParams = array_filter([
            'id' => $params['id'] ?? null,
            'no' => $params['no'] ?? null,
        ]);

        $url = $this->baseUrl . '/item/detail.do?' . http_build_query($queryParams);

        try {
            $response = $this->client->get($url, ['headers' => $headers]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to get item detail: ' . $e->getMessage());
        }
    }
}
