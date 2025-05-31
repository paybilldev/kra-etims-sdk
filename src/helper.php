<?php

use Illuminate\Support\Facades\Log;
use KraEtimsSdk\Exceptions\ApiException;
use KraEtimsSdk\Exceptions\AuthenticationException;
use KraEtimsSdk\Exceptions\ValidationException;

function handleKraError(array $response, string $operation = ''): array
{
    if (empty($response)) {
        throw new ApiException('Invalid API response', 500);
    }

    if (isset($response['resultCd']) && $response['resultCd'] !== '0000') {
        Log::error("API Error [{$operation}]: " . ($response['resultMsg'] ?? 'Unknown error'));
        throw new ApiException(
            $response['resultMsg'] ?? 'API Error',
            400,
            $response['resultCd'] ?? null,
            $response
        );
    }

    return $response;
}

function formatKraError(Throwable $e): array
{
    if ($e instanceof ApiException) {
        return [
            'success' => false,
            'error' => [
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
                'details' => $e->getDetails(),
            ],
            'statusCode' => $e->getStatusCode(),
        ];
    }

    if ($e instanceof ValidationException) {
        return [
            'success' => false,
            'error' => [
                'message' => $e->getMessage(),
                'validationErrors' => $e->getErrors(),
            ],
            'statusCode' => 400,
        ];
    }

    if ($e instanceof AuthenticationException) {
        return [
            'success' => false,
            'error' => [
                'message' => $e->getMessage(),
            ],
            'statusCode' => $e->getStatusCode(),
        ];
    }

    return [
        'success' => false,
        'error' => [
            'message' => $e->getMessage() ?? 'Internal Server Error',
        ],
        'statusCode' => 500,
    ];
}