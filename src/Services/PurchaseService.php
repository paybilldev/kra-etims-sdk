<?php

namespace KraEtimsSdk\Services;

class PurchaseService
{
    private $apiService;
    private $validator;

    public function __construct(public string $baseUrl, public string $username, public string $password) {
        $this->apiService = new ApiService($baseUrl, $username, $password);
        $this->validator = new Validator();
    }

    /**
     * Get purchase transaction information.
     *
     * @param array $data
     * @return array
     * @throws ValidationException|ApiException
     */
    public function selectPurchaseTrns(array $data): array
    {
        try {
            error_log('Getting purchase transaction information');

            // Validate request data using taxpayerInfo schema
            $validatedData = $this->validator->validate($data, 'taxpayerInfo');

            // Prepare headers or additional parameters
            $headers = [
                'tin'    => $validatedData['tin'],
                'bhfId'  => $validatedData['bhfId'],
                'cmcKey' => $data['cmcKey'] ?? null,
            ];

            // Make the POST request
            $response = $this->apiService->post(
                '/etims-oscu/v1/selectPurchaseTrns',
                $validatedData,
                $headers
            );

            return [
                'success' => true,
                'data' => $response,
            ];
        } catch (\Exception $e) {
            error_log('Get purchase transaction error: ' . $e->getMessage());
            throw $e;
        }
    }
}
