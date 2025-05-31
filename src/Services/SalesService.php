<?php

namespace KraEtimsSdk\Services;

class SalesService
{
    private $apiService;
    private $validator;

    public function __construct(public string $baseUrl, public string $username, public string $password) {
        $this->apiService = new ApiService($baseUrl, $username, $password);
        $this->validator = new Validator();
    }

    /**
     * Send sales transaction information.
     *
     * @param array $data
     * @return array
     * @throws ValidationException|ApiException
     */
    public function sendSalesTrns(array $data): array
    {
        try {
            error_log('Sending sales transaction information');

            // Validate request data using 'salesTrns' schema
            $validatedData = $this->validator->validate($data, 'salesTrns');

            $headers = [
                'tin'    => $validatedData['tin'],
                'bhfId'  => $validatedData['bhfId'],
                'cmcKey' => $data['cmcKey'] ?? null,
            ];

            $response = $this->apiService->post(
                '/etims-oscu/v1/sendSalesTrns',
                $validatedData,
                $headers
            );

            return [
                'success' => true,
                'data' => $response,
            ];
        } catch (\Exception $e) {
            error_log('Send sales transaction error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get sales transaction information.
     *
     * @param array $data
     * @return array
     * @throws ValidationException|ApiException
     */
    public function selectSalesTrns(array $data): array
    {
        try {
            error_log('Getting sales transaction information');

            // Validate request data using 'selectSalesTrns' schema
            $validatedData = $this->validator->validate($data, 'selectSalesTrns');

            $headers = [
                'tin'    => $validatedData['tin'],
                'bhfId'  => $validatedData['bhfId'],
                'cmcKey' => $data['cmcKey'] ?? null,
            ];

            $response = $this->apiService->post(
                '/etims-oscu/v1/selectSalesTrns',
                $validatedData,
                $headers
            );

            return [
                'success' => true,
                'data' => $response,
            ];
        } catch (\Exception $e) {
            error_log('Get sales transaction error: ' . $e->getMessage());
            throw $e;
        }
    }
}
