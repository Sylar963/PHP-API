<?php

declare(strict_types=1);

namespace App\Application\Commands;

class UpdateTeamCommand
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $name = null,
        public readonly ?string $description = null
    ) {
    }
}
