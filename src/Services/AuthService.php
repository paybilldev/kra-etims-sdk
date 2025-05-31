<?php

namespace KraEtimsSdk\Services;

use KraEtimsSdk\Exceptions\ValidationException;

class AuthService {
    private $apiService;
    private $validator;

    public function __construct(public string $baseUrl, public string $username, public string $password) {
        $this->apiService = new ApiService($baseUrl, $username, $password);
        $this->validator = new Validator();
    }

    /**
     * Get authentication token
     * @param array $credentials ['username' => string, 'password' => string]
     * @return array ['success' => bool, 'data' => ['access_token' => string, 'expires_at' => int]]
     * @throws Exception on validation or authentication failure
     */
    public function getToken(array $credentials): array {
        try {
            // Validate credentials
            $validatedData = $this->validator->validate($credentials, 'auth');

            // Set credentials on apiService
            $this->apiService->username = $validatedData['username'];
            $this->apiService->password = $validatedData['password'];

            // Authenticate and get token
            $token = $this->apiService->authenticate();

            return [
                'success' => true,
                'data' => [
                    'access_token' => $token,
                    'expires_at' => $this->apiService->tokenExpiry
                ]
            ];
        } catch (ValidationException $ve) {
            error_log("Validation error in AuthService: " . implode(", ", $ve->getErrors()));
            throw $ve;
        } catch (\Exception $ex) {
            error_log("Authentication error in AuthService: " . $ex->getMessage());
            throw $ex;
        }
    }
}
