<?php

namespace KraEtimsSdk\Services;

class InitializationService
{
    private $apiService;
    private $validator;

    public function __construct(public string $baseUrl, public string $username, public string $password) {
        $this->apiService = new ApiService($baseUrl, $username, $password);
        $this->validator = new Validator();
    }

    /**
     * Initialize OSDC Info
     * 
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function selectInitOsdcInfo(array $data): array
    {
        try {
            error_log('Initializing OSDC Info');

            $validated = $this->validator->validate($data, 'initialization');

            $endpoint = '/etims-oscu/v1/selectInitOsdcInfo';

            $response = $this->apiService->post($endpoint, $validated);

            return [
                'success' => true,
                'data' => $response
            ];
        } catch (\Exception $e) {
            error_log('Initialization error: ' . $e->getMessage());
            throw $e;
        }
    }
}
