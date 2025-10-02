<?php

declare(strict_types=1);

namespace App\Application\Commands;

use DateTimeImmutable;

class CreateTaskCommand
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $status,
        public readonly string $priority,
        public readonly string $projectId,
        public readonly ?string $assignedTo = null,
        public readonly ?DateTimeImmutable $dueDate = null
    ) {
    }
}
