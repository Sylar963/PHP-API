<?php

declare(strict_types=1);

namespace App\Application\Commands;

class CreateUserCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $role
    ) {
    }
}
