<?php

declare(strict_types=1);

namespace App\Application\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public function __construct(string $message = "Validation failed", private array $errors = [])
    {
        parent::__construct($message, 422);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
