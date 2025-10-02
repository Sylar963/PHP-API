<?php

declare(strict_types=1);

namespace App\Application\Commands;

class LoginUserCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $deviceName = null
    ) {
    }
}
