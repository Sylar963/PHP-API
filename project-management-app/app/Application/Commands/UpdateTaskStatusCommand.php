<?php

declare(strict_types=1);

namespace App\Application\Commands;

class UpdateTaskStatusCommand
{
    public function __construct(
        public readonly string $taskId,
        public readonly string $newStatus,
        public readonly string $updatedBy
    ) {
    }
}
