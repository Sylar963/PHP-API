<?php

declare(strict_types=1);

namespace App\Application\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    public function __construct(string $message = "Unauthorized access")
    {
        parent::__construct($message, 403);
    }
}
