<?php

declare(strict_types=1);

namespace App\Domain\Events;

use DateTimeImmutable;

class TaskAssigned
{
    public function __construct(
        public readonly string $taskId,
        public readonly string $taskTitle,
        public readonly string $assignedTo,
        public readonly string $assignedBy,
        public readonly string $projectId,
        public readonly DateTimeImmutable $occurredAt
    ) {
    }

    public static function create(
        string $taskId,
        string $taskTitle,
        string $assignedTo,
        string $assignedBy,
        string $projectId
    ): self {
        return new self(
            taskId: $taskId,
            taskTitle: $taskTitle,
            assignedTo: $assignedTo,
            assignedBy: $assignedBy,
            projectId: $projectId,
            occurredAt: new DateTimeImmutable()
        );
    }
}
