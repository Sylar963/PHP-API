<?php

declare(strict_types=1);

namespace App\Application\Commands;

use DateTimeImmutable;

class CreateProjectCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $status,
        public readonly string $ownerId,
        public readonly ?DateTimeImmutable $startDate = null,
        public readonly ?DateTimeImmutable $endDate = null
    ) {
    }
}
