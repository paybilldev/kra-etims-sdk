# KRA eTims Integration SDK

A PHP SDK for integrating with the Kenya Revenue Authority (KRA) Electronic Tax Invoice Management System (eTims) API for Paybill Kenya but can generally be used in any php project.

## Author

Bartile Emmanuel

Email: ebartile@gmail.com

Tel: +254757807150

## Introduction to KRA eTims

Kenya Revenue Authority currently uses an Integrated Software that collects and manages domestic tax revenues. The Electronic Tax Invoice Management System (eTims) has introduced a supply chain management capability and integration with other KRA systems.

### Area of Application

KRA seeks to introduce the usage of Online and Virtual Sales Control Unit (OSCU & VSCU) capable of handling a richer dataset than the traditional Electronic Tax Register system. This system includes specific requirements concerning the Trader Invoicing System to be used together with an Online or Virtual Sales Control Unit.

### Key Definitions

- **Authority**: Kenya Revenue Authority
- **User**: A taxpayer, user of Trader Invoicing System (TIS)
- **PIN**: Personal Identification Number
- **Electronic Tax Invoicing Management System**: A system comprising of Trader Invoicing System and Online/Virtual Sales Control Unit connected together
- **Trader Invoicing System (TIS)**: A system designated for use in business for efficiency management controls in areas of invoicing and stock management
- **Receipt**: A Tax Invoice or a receipt for the provision of goods/services provided to the customer
- **Online & Virtual Sales Control Unit (OSCU & VSCU)**: A software module communicating with both the TIS and the Authority for processing receipts

### Receipt Types

Each receipt issued by Trader Invoicing System is formed from a combination of receipt type and transaction type:

1. Receipt types: NORMAL, COPY, TRAINING, PROFORMA
2. Transaction types: SALE, CREDIT NOTE, DEBIT NOTE

### Receipt Labels

| RECEIPT TYPE | TRANSACTION TYPE | RECEIPT LABEL |
|--------------|------------------|---------------|
| NORMAL       | SALES            | NS            |
| NORMAL       | CREDIT NOTE      | NC            |
| COPY         | SALES            | CS            |
| COPY         | CREDIT NOTE      | CC            |
| TRAINING     | SALES            | TS            |
| TRAINING     | CREDIT NOTE      | TC            |
| PROFORMA     | SALES            | PS            |

## Features

- Complete implementation of KRA eTims API endpoints
- Comprehensive validation of requests
- Error handling and logging
- Authentication management
- Modular architecture

## Installation

```bash
# Clone the repository
git clone https://github.com/paybilldev/kra-etims-sdk.git

# Navigate to the project directory
cd kra-etims-sdk

# Install dependencies
composer install
```

## Project Structure

```
kra-etims-sdk/
├── examples/                           # Example usage scripts
│   ├── basic.php                       # SDK usage as a library
│   └── composer.json                   # Composer File
├── src/                                # Source code
│   ├── services/                       # Business logic services
│   │   ├── ApiService.php              # Core API service
│   │   ├── AuthService.php             # Authentication service
│   │   ├── BasicDataService.php        # Basic data service
│   │   ├── InitializationService.php   # Initialization service
│   │   ├── PurchaseService.php         # Purchase service
│   │   ├── SalesService.php            # Sales service
│   │   └── StockService.php            # Stock service
│   ├── Exceptions/                     # Utility functions
│   │   ├── ApiException.php            # Error handling
│   │   ├── AuthenticationException.php # Error handling
│   │   └── ValidationException.js      # Request validation
│   └── helper.php                      # Helper Functions
├── tests/                              # Test files
│   └── ApiTest.js                      # API service tests
├── composer.json                       # Project metadata and dependencies
└── README.md                           # SDK docs
```

## Usage

### As a PHP SDK

```php
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
```

## TIS Specifications and Requirements

### Data Flow Between TIS and OSCU/VSCU

For the purpose of signing an invoice, the data flow between the Trader Invoicing System and the Virtual Sales Control Unit will be as follows for each receipt type:

1. **TIS sends receipt data to OSCU/VSCU**:
   - Date and time
   - Personal Identification Number
   - Buyer's PIN (Optional)
   - Receipt number
   - Receipt type and transaction type
   - Tax rates
   - Total amounts with tax
   - Tax amounts

2. **OSCU/VSCU receives receipt data from TIS**

3. **OSCU/VSCU generates response data and sends it back to TIS**:
   - SCU ID
   - Date and time
   - Receipt label
   - Receipt counter per receipt type
   - Receipt counter of all receipts
   - Digital signatures (except for TRAINING and PROFORMA receipts)

4. **TIS finalizes receipt** by printing OSCU/VSCU information on the receipt

5. **TIS sends complete journal data** of NS and NC receipt labels in text form to OSCU/VSCU

### Receipt Requirements

A receipt must show the following minimum required information:

1. Taxpayer's name
2. Personal Identification Number
3. The address at which the sale takes place
4. Personal Identification number of the buyer
5. Receipt type and transaction type
6. Serial number of the receipt from an uninterrupted ascending number series per receipt type
7. Registered items and/or services with description, quantity, price, with any other action that may be done such as cancellations, corrections
8. Total sales amount
9. Tax rates applied
10. The value added tax added to the sale amount
11. Means of payment
12. SCU information:
    - Date and time stamped by OSCU/VSCU
    - Sequential receipt type number
    - Receipt signatures
    - OSCU/VSCU identification number
13. Date and time stamped by TIS

### TIS Functional Requirements

1. TIS shall not issue a receipt of any type before the data flow with OSCU/VSCU has been finalized
2. Copies, training, or proforma receipts must be clearly distinguishable from normal receipts
3. All corrections on the receipt should be performed before approving it
4. Normal Sale (NS) refers to a receipt that shall be produced and offered to the client
5. TIS shall not be able to register a sales amount without simultaneously printing a receipt
6. TIS shall not be able to register the amount of a transaction without identifying the good and/or service
7. TIS shall not be able to correct a transaction without prior cancelation of the original transaction
8. TIS shall print only one original receipt. Reprints shall have a watermark with mention "Copy"
9. TIS shall not issue a receipt of goods when the corresponding stock is less than the requested quantity

## Development

```bash
# Run in development mode with auto-reload
composer install

# Run tests
vendor/bin/phpunit tests/ApiTest.php 

# I plan to add more tests in the future.
```

## License

MIT


