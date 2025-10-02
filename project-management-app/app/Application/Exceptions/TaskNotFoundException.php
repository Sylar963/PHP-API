<?php

declare(strict_types=1);

namespace App\Application\Exceptions;

use Exception;

class TaskNotFoundException extends Exception
{
    public function __construct(string $message = "Task not found")
    {
        parent::__construct($message, 404);
    }
}
