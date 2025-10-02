<?php

declare(strict_types=1);

namespace App\Domain\Events;

use DateTimeImmutable;

class ProjectCreated
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $projectName,
        public readonly string $ownerId,
        public readonly DateTimeImmutable $occurredAt
    ) {
    }

    public static function create(
        string $projectId,
        string $projectName,
        string $ownerId
    ): self {
        return new self(
            projectId: $projectId,
            projectName: $projectName,
            ownerId: $ownerId,
            occurredAt: new DateTimeImmutable()
        );
    }
}
