<?php

namespace KraEtimsSdk\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected int $statusCode;
    protected ?string $errorCode;
    protected mixed $details;

    public function __construct(
        string $message = 'API Error',
        int $statusCode = 400,
        string|null $errorCode = null,
        mixed $details = null
    ) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
        $this->details = $details;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getDetails(): mixed
    {
        return $this->details;
    }
}
