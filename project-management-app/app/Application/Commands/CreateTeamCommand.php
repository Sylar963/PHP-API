<?php

declare(strict_types=1);

namespace App\Application\Commands;

class CreateTeamCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $description
    ) {
    }
}
