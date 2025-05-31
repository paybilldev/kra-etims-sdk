<?php

namespace KraEtimsSdk\Services;

use KraEtimsSdk\Exceptions\AuthenticationException;

class ApiService
{
    public ?string $token = null;
    public ?int $tokenExpiry = null;

    public function __construct(public string $baseUrl, public string $username, public string $password) {}

    /**
     * Check if the current token is valid
     */
    public function isTokenValid(): bool
    {
        if (!$this->token || !$this->tokenExpiry) {
            return false;
        }

        // 5 minute buffer
        return time() < ($this->tokenExpiry - 300);
    }

    /**
     * Authenticate and store access token
     *
     * @throws AuthenticationException
     */
    public function authenticate(): string
    {
        try {
            error_log('Authenticating with KRA eTims API');

            $url = rtrim($this->baseUrl, '/') . '/oauth2/v1/generate';

            $headers = [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password)
            ];

            $postData = http_build_query(['grant_type' => 'client_credentials']);

            $response = $this->curlRequest('POST', $url, $postData, $headers);

            if (empty($response['access_token'])) {
                throw new AuthenticationException('Invalid authentication response from KRA');
            }

            $this->token = $response['access_token'];
            $this->tokenExpiry = time() + ($response['expires_in'] ?? 3600);

            error_log('Successfully authenticated with KRA eTims API');

            return $this->token;
        } catch (\Throwable $e) {
            error_log("Authentication failed: " . $e->getMessage());
            throw new AuthenticationException('Failed to authenticate with KRA eTims API');
        }
    }

    /**
     * Make a general API request
     *
     * @param string $method HTTP method: GET, POST, PUT, DELETE
     * @param string $endpoint API endpoint path
     * @param array|string $data Query parameters or JSON payload
     * @param array $headers Additional headers
     * @return array Response decoded as array
     */
    public function request(string $method, string $endpoint, $data = [], array $headers = []): array
    {
        try {
            error_log("Making {$method} request to {$endpoint}");

            if (!$this->isTokenValid()) {
                $this->authenticate();
            }

            $defaultHeaders = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
            ];

            $allHeaders = array_merge($defaultHeaders, $this->formatHeaders($headers));

            $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

            if (strtoupper($method) === 'GET' && is_array($data) && count($data) > 0) {
                $url .= '?' . http_build_query($data);
                $data = null; // no body for GET
            }

            if (is_array($data) && strtoupper($method) !== 'GET') {
                $data = json_encode($data);
            }

            $response = $this->curlRequest(strtoupper($method), $url, $data, $allHeaders);

            return handleKraError($response, $endpoint);
        } catch (\Throwable $e) {
            error_log("API request error: {$e->getMessage()}");

            // You may add more sophisticated error handling here if you have access to response body

            throw $e;
        }
    }

    /**
     * Shortcut for GET requests
     */
    public function get(string $endpoint, array $params = [], array $headers = []): array
    {
        return $this->request('GET', $endpoint, $params, $headers);
    }

    /**
     * Shortcut for POST requests
     */
    public function post(string $endpoint, array $payload = [], array $headers = []): array
    {
        return $this->request('POST', $endpoint, $payload, $headers);
    }

    /**
     * Helper: Format headers array from key=>value to ["Key: Value"]
     */
    public function formatHeaders(array $headers): array
    {
        $formatted = [];
        foreach ($headers as $key => $value) {
            $formatted[] = "{$key}: {$value}";
        }
        return $formatted;
    }

    /**
     * Helper: Send a CURL request
     *
     * @param string $method HTTP method
     * @param string $url Full URL
     * @param string|null $body JSON-encoded body or form-encoded data
     * @param array $headers Array of headers (each like "Key: Value")
     * @return array Decoded JSON response
     * @throws \RuntimeException on CURL error or JSON decode failure
     */
    protected function curlRequest(string $method, string $url, ?string $body = null, array $headers = []): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (in_array($method, ['POST', 'PUT', 'DELETE']) && $body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $responseBody = curl_exec($ch);

        if ($responseBody === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException("Curl error: {$err}");
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode($responseBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Failed to decode JSON response: " . json_last_error_msg());
        }

        // Optionally you can check for HTTP errors here:
        if ($httpCode < 200 || $httpCode >= 300) {
            // You may want to throw an exception or handle errors differently
            error_log("HTTP error {$httpCode} from API: {$responseBody}");
        }

        return $decoded;
    }
}
