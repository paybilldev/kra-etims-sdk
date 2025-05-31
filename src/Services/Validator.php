<?php

namespace KraEtimsSdk\Services;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use KraEtimsSdk\Exceptions\ValidationException;

class Validator {
    // Define schemas as arrays of validators
    private $schemas = [];

    public function __construct() {
        // Define schemas similar to your JS Joi schemas

        $this->schemas = [
            'tin' => v::stringType()->notEmpty()->setName('Taxpayer Identification Number'),
            'bhfId' => v::stringType()->notEmpty()->setName('Branch ID'),
            'lastReqDt' => v::stringType()->notEmpty()->setName('Last Request Date'),
            'dvcSrlNo' => v::stringType()->notEmpty()->setName('Device Serial Number'),

            'auth' => v::key('username', v::stringType()->notEmpty())
                       ->key('password', v::stringType()->notEmpty()),

            'initialization' => v::key('tin', v::stringType()->notEmpty())
                                ->key('bhfId', v::stringType()->notEmpty())
                                ->key('dvcSrlNo', v::stringType()->notEmpty()),

            'codeList' => v::key('tin', v::stringType()->notEmpty())
                           ->key('bhfId', v::stringType()->notEmpty())
                           ->key('lastReqDt', v::stringType()->notEmpty()),

            'itemClsList' => v::key('tin', v::stringType()->notEmpty())
                              ->key('bhfId', v::stringType()->notEmpty())
                              ->key('lastReqDt', v::stringType()->notEmpty()),

            'bhfList' => v::key('lastReqDt', v::stringType()->notEmpty()),

            'noticeList' => v::key('tin', v::stringType()->notEmpty())
                             ->key('bhfId', v::stringType()->notEmpty())
                             ->key('lastReqDt', v::stringType()->notEmpty()),

            'taxpayerInfo' => v::key('tin', v::stringType()->notEmpty())
                               ->key('bhfId', v::stringType()->notEmpty())
                               ->key('lastReqDt', v::stringType()->notEmpty()),

            'customerList' => v::key('tin', v::stringType()->notEmpty())
                              ->key('bhfId', v::stringType()->notEmpty())
                              ->key('lastReqDt', v::stringType()->notEmpty()),

            'salesTrns' => v::key('tin', v::stringType()->notEmpty())
                            ->key('bhfId', v::stringType()->notEmpty())
                            ->key('invcNo', v::stringType()->notEmpty()->setName('Invoice Number'))
                            ->key('salesTrnsItems', v::arrayType()->notEmpty()->each(
                                v::key('itemCd', v::stringType()->notEmpty()->setName('Item Code'))
                                 ->key('itemNm', v::stringType()->notEmpty()->setName('Item Name'))
                                 ->key('qty', v::NumericVal()->notEmpty()->setName('Quantity'))
                                 ->key('prc', v::NumericVal()->notEmpty()->setName('Price'))
                                 ->key('splyAmt', v::NumericVal()->notEmpty()->setName('Supply Amount'))
                                 ->key('dcRt', v::optional(v::NumericVal()))
                                 ->key('dcAmt', v::optional(v::NumericVal()))
                                 ->key('taxTyCd', v::stringType()->notEmpty()->setName('Tax Type Code'))
                                 ->key('taxAmt', v::NumericVal()->notEmpty()->setName('Tax Amount'))
                            )->setName('Sales Transaction Items')),

            'selectSalesTrns' => v::key('tin', v::stringType()->notEmpty())
                                  ->key('bhfId', v::stringType()->notEmpty())
                                  ->key('lastReqDt', v::stringType()->notEmpty())
                                  ->key('invcNo', v::optional(v::stringType())),

            'moveList' => v::key('tin', v::stringType()->notEmpty())
                           ->key('bhfId', v::stringType()->notEmpty())
                           ->key('lastReqDt', v::stringType()->notEmpty()),

            'stockMaster' => v::key('tin', v::stringType()->notEmpty())
                              ->key('bhfId', v::stringType()->notEmpty())
                              ->key('itemCd', v::stringType()->notEmpty())
                              ->key('itemClsCd', v::stringType()->notEmpty())
                              ->key('itemNm', v::stringType()->notEmpty())
                              ->key('pkgUnitCd', v::stringType()->notEmpty())
                              ->key('qtyUnitCd', v::stringType()->notEmpty())
                              ->key('splyAmt', v::NumericVal()->notEmpty())
                              ->key('vatTyCd', v::stringType()->notEmpty())
        ];
    }

    /**
     * Validate data against schema name
     * @param array $data
     * @param string $schemaName
     * @return array validated data
     * @throws ValidationError
     */
    public function validate(array $data, string $schemaName): array {
        if (!isset($this->schemas[$schemaName])) {
            throw new \Exception("Validation schema '$schemaName' does not exist.");
        }

        $validator = $this->schemas[$schemaName];

        try {
            $validator->assert($data);
            return $data; // return original data if validation passes
        } catch (NestedValidationException $exception) {
            $errors = [];
            foreach ($exception->getMessages() as $message) {
                $errors[] = $message;
            }
            throw new ValidationException("Validation failed", $errors);
        }
    }
}
