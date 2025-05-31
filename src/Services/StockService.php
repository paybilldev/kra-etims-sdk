<?php

namespace KraEtimsSdk\Services;

class StockService
{
    private $apiService;
    private $validator;

    public function __construct(public string $baseUrl, public string $username, public string $password) {
        $this->apiService = new ApiService($baseUrl, $username, $password);
        $this->validator = new Validator();
    }

    /**
     * Get move list
     *
     * @param array $data
     * @return array
     * @throws \Throwable
     */
    public function selectMoveList(array $data): array
    {
        try {
            error_log('Getting move list');

            $validatedData = $this->validator->validate($data, 'moveList');

            $headers = [
                'tin'    => $validatedData['tin'],
                'bhfId'  => $validatedData['bhfId'],
                'cmcKey' => $data['cmcKey'] ?? null,
            ];

            $response = $this->apiService->post(
                '/etims-oscu/v1/selectMoveList',
                $validatedData,
                $headers
            );

            return [
                'success' => true,
                'data' => $response,
            ];
        } catch (\Throwable $e) {
            error_log('Get move list error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Save stock master
     *
     * @param array $data
     * @return array
     * @throws \Throwable
     */
    public function saveStockMaster(array $data): array
    {
        try {
            error_log('Saving stock master');

            $validatedData = $this->validator->validate($data, 'stockMaster');

            $headers = [
                'tin'    => $validatedData['tin'],
                'bhfId'  => $validatedData['bhfId'],
                'cmcKey' => $data['cmcKey'] ?? null,
            ];

            $response = $this->apiService->post(
                '/etims-oscu/v1/saveStockMaster',
                $validatedData,
                $headers
            );

            return [
                'success' => true,
                'data' => $response,
            ];
        } catch (\Throwable $e) {
            error_log('Save stock master error: ' . $e->getMessage());
            throw $e;
        }
    }
}
