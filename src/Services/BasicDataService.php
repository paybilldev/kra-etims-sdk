<?php

namespace KraEtimsSdk\Services;

class BasicDataService
{
    private $apiService;
    private $validator;

    public function __construct(public string $baseUrl, public string $username, public string $password) {
        $this->apiService = new ApiService($baseUrl, $username, $password);
        $this->validator = new Validator();
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function selectCodeList(array $data): array
    {
        error_log('Getting code list');

        $validatedData = $this->validator->validate($data, 'codeList');

        $response = $this->apiService->post(
            '/etims-oscu/v1/selectCodeList',
            $validatedData,
            [
                'tin' => $validatedData['tin'],
                'bhfId' => $validatedData['bhfId'],
                'cmcKey' => $data['cmcKey'] ?? null,
            ]
        );

        return ['success' => true, 'data' => $response];
    }

    public function selectItemClsList(array $data): array
    {
        error_log('Getting item classification list');

        $validatedData = $this->validator->validate($data, 'itemClsList');

        $response = $this->apiService->post(
            '/etims-oscu/v1/selectItemClsList',
            $validatedData,
            [
                'tin' => $validatedData['tin'],
                'bhfId' => $validatedData['bhfId'],
                'cmcKey' => $data['cmcKey'] ?? null,
            ]
        );

        return ['success' => true, 'data' => $response];
    }

    public function selectBhfList(array $data): array
    {
        error_log('Getting branch list');

        $validatedData = $this->validator->validate($data, 'bhfList');

        $response = $this->apiService->post(
            '/etims-oscu/v1/selectBhfList',
            $validatedData
        );

        return ['success' => true, 'data' => $response];
    }

    public function selectNoticeList(array $data): array
    {
        error_log('Getting notice list');

        $validatedData = $this->validator->validate($data, 'noticeList');

        $response = $this->apiService->post(
            '/etims-oscu/v1/selectNoticeList',
            $validatedData,
            [
                'tin' => $validatedData['tin'],
                'bhfId' => $validatedData['bhfId'],
                'cmcKey' => $data['cmcKey'] ?? null,
            ]
        );

        return ['success' => true, 'data' => $response];
    }

    public function selectTaxpayerInfo(array $data): array
    {
        error_log('Getting taxpayer info');

        $validatedData = $this->validator->validate($data, 'taxpayerInfo');

        $response = $this->apiService->post(
            '/etims-oscu/v1/selectTaxpayerInfo',
            $validatedData,
            [
                'tin' => $validatedData['tin'],
                'bhfId' => $validatedData['bhfId'],
                'cmcKey' => $data['cmcKey'] ?? null,
            ]
        );

        return ['success' => true, 'data' => $response];
    }

    public function selectCustomerList(array $data): array
    {
        error_log('Getting customer list');

        $validatedData = $this->validator->validate($data, 'customerList');

        $response = $this->apiService->post(
            '/etims-oscu/v1/selectCustomerList',
            $validatedData,
            [
                'tin' => $validatedData['tin'],
                'bhfId' => $validatedData['bhfId'],
                'cmcKey' => $data['cmcKey'] ?? null,
            ]
        );

        return ['success' => true, 'data' => $response];
    }
}
