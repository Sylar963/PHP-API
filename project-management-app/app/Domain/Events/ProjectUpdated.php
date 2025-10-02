<?php

declare(strict_types=1);

namespace App\Domain\Events;

use DateTimeImmutable;

class ProjectUpdated
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $projectName,
        public readonly string $updatedBy,
        public readonly array $changes,
        public readonly DateTimeImmutable $occurredAt
    ) {
    }

    public static function create(
        string $projectId,
        string $projectName,
        string $updatedBy,
        array $changes = []
    ): self {
        return new self(
            projectId: $projectId,
            projectName: $projectName,
            updatedBy: $updatedBy,
            changes: $changes,
            occurredAt: new DateTimeImmutable()
        );
    }
}
