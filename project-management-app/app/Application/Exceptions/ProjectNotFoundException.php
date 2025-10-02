<?php

declare(strict_types=1);

namespace App\Application\Exceptions;

use Exception;

class ProjectNotFoundException extends Exception
{
    public function __construct(string $message = "Project not found")
    {
        parent::__construct($message, 404);
    }
}
