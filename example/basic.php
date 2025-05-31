<?php

require_once __DIR__ . '/vendor/autoload.php';

use KraEtimsSdk\Services\AuthService;
use KraEtimsSdk\Services\BasicDataService;
use KraEtimsSdk\Services\InitializationService;
use KraEtimsSdk\Services\SalesService;

class KRAeTimsExample
{
    private $auth;
    private $initialization;
    private $basicData;
    private $sales;

     public function __construct(public string $baseUrl, public string $username, public string $password) {
        $this->auth = new AuthService($baseUrl, $username, $password);
        $this->initialization = new InitializationService($baseUrl, $username, $password);
        $this->basicData = new BasicDataService($baseUrl, $username, $password);
        $this->sales = new SalesService($baseUrl, $username, $password);
    }

    public function authenticate()
    {
        echo "Authenticating with KRA eTims API...\n";
        try {
            $result = $this->auth->getToken([
                'username' => $this->username,
                'password' => $this->password
            ]);
            echo "Authentication successful:\n";
            print_r($result);
            return $result['data']['access_token'];
        } catch (Exception $e) {
            echo "Authentication failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function initializeOsdc()
    {
        echo "Initializing OSDC Info...\n";
        try {
            $result = $this->initialization->selectInitOsdcInfo([
                'tin' => 'P000000045R',
                'bhfId' => '00',
                'dvcSrlNo' => 'MOVA22'
            ]);
            echo "Initialization successful:\n";
            print_r($result);
            return $result;
        } catch (Exception $e) {
            echo "Initialization failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function getCodeList()
    {
        echo "Getting code list...\n";
        try {
            $result = $this->basicData->selectCodeList([
                'tin' => 'P000000045R',
                'bhfId' => '00',
                'lastReqDt' => '20220101010101'
            ]);
            echo "Code list retrieved:\n";
            print_r($result);
            return $result;
        } catch (Exception $e) {
            echo "Failed to retrieve code list: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function sendSalesTrns()
    {
        echo "Sending sales transaction...\n";
        try {
            $result = $this->sales->sendSalesTrns([
                'tin' => 'P000000045R',
                'bhfId' => '00',
                'invcNo' => 'INV001',
                'salesTrnsItems' => [
                    [
                        'itemCd' => 'ITEM001',
                        'itemNm' => 'Test Item',
                        'qty' => 1,
                        'prc' => 100,
                        'splyAmt' => 100,
                        'taxTyCd' => 'V',
                        'taxAmt' => 16
                    ]
                ]
            ]);
            echo "Sales transaction sent:\n";
            print_r($result);
            return $result;
        } catch (Exception $e) {
            echo "Failed to send sales transaction: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}

# API Base URLs
// DEV_API_BASE_URL=https://etims-api-sbx.kra.go.ke
// PROD_API_BASE_URL=https://etims-api.kra.go.ke/etims-api

// # Authentication
// API_USERNAME=your_username
// API_PASSWORD=your_password

// If script is run directly
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    $baseUrl = 'https://etims-api-sbx.kra.go.ke'; 
    $username = 'test';
    $password = 'test';
    $example = new KRAeTimsExample($baseUrl, $username, $password);
    try {
        $example->authenticate();
        $example->initializeOsdc();
        $example->getCodeList();
        $example->sendSalesTrns();
        echo "All examples completed successfully.\n";
    } catch (Exception $e) {
        echo "Example execution failed: " . $e->getMessage() . "\n";
    }
}
