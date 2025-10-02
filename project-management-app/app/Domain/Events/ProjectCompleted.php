<?php

declare(strict_types=1);

namespace App\Domain\Events;

use DateTimeImmutable;

class ProjectCompleted
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $projectName,
        public readonly string $completedBy,
        public readonly int $totalTasks,
        public readonly DateTimeImmutable $occurredAt
    ) {
    }

    public static function create(
        string $projectId,
        string $projectName,
        string $completedBy,
        int $totalTasks
    ): self {
        return new self(
            projectId: $projectId,
            projectName: $projectName,
            completedBy: $completedBy,
            totalTasks: $totalTasks,
            occurredAt: new DateTimeImmutable()
        );
    }
}
