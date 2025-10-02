<?php

declare(strict_types=1);

namespace App\Application\Commands;

use DateTimeImmutable;

class UpdateTaskCommand
{
    public function __construct(
        public readonly string $taskId,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?string $status = null,
        public readonly ?string $priority = null,
        public readonly ?DateTimeImmutable $dueDate = null
    ) {
    }
}
