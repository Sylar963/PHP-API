<?php

declare(strict_types=1);

namespace App\Domain\Events;

use DateTimeImmutable;

class TaskCreated
{
    public function __construct(
        public readonly string $taskId,
        public readonly string $title,
        public readonly string $projectId,
        public readonly ?string $assignedTo,
        public readonly DateTimeImmutable $occurredAt
    ) {
    }

    public static function create(
        string $taskId,
        string $title,
        string $projectId,
        ?string $assignedTo = null
    ): self {
        return new self(
            taskId: $taskId,
            title: $title,
            projectId: $projectId,
            assignedTo: $assignedTo,
            occurredAt: new DateTimeImmutable()
        );
    }
}
