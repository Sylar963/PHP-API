<?php

declare(strict_types=1);

namespace App\Application\Commands;

class AssignTaskCommand
{
    public function __construct(
        public readonly string $taskId,
        public readonly string $userId,
        public readonly string $assignedBy
    ) {
    }
}
