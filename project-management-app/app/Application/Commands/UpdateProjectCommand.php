<?php

declare(strict_types=1);

namespace App\Application\Commands;

use DateTimeImmutable;

class UpdateProjectCommand
{
    public function __construct(
        public readonly string $projectId,
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $status = null,
        public readonly ?DateTimeImmutable $startDate = null,
        public readonly ?DateTimeImmutable $endDate = null
    ) {
    }
}
