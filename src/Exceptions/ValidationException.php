<?php

namespace KraEtimsSdk\Exceptions;

use Exception;

class ValidationException extends Exception
{
    protected array $validationErrors;

    public function __construct(string $message = "Validation error", array $errors = [])
    {
        parent::__construct($message);
        $this->validationErrors = $errors;
    }

    public function getErrors(): array
    {
        return $this->validationErrors;
    }
}
