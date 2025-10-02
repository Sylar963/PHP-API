<?php

declare(strict_types=1);

namespace App\Domain\Events;

use DateTimeImmutable;

class TaskStatusChanged
{
    public function __construct(
        public readonly string $taskId,
        public readonly string $taskTitle,
        public readonly string $oldStatus,
        public readonly string $newStatus,
        public readonly string $changedBy,
        public readonly DateTimeImmutable $occurredAt
    ) {
    }

    public static function create(
        string $taskId,
        string $taskTitle,
        string $oldStatus,
        string $newStatus,
        string $changedBy
    ): self {
        return new self(
            taskId: $taskId,
            taskTitle: $taskTitle,
            oldStatus: $oldStatus,
            newStatus: $newStatus,
            changedBy: $changedBy,
            occurredAt: new DateTimeImmutable()
        );
    }
}
