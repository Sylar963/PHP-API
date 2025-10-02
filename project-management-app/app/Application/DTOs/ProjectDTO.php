<?php

declare(strict_types=1);

namespace App\Application\DTOs;

use DateTimeImmutable;

class ProjectDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $status,
        public readonly string $ownerId,
        public readonly ?DateTimeImmutable $startDate,
        public readonly ?DateTimeImmutable $endDate,
        public readonly DateTimeImmutable $createdAt,
        public readonly DateTimeImmutable $updatedAt
    ) {
    }

    public static function fromEntity($project): self
    {
        return new self(
            id: $project->getId(),
            name: $project->getName(),
            description: $project->getDescription(),
            status: $project->getStatus(),
            ownerId: $project->getOwnerId(),
            startDate: $project->getStartDate(),
            endDate: $project->getEndDate(),
            createdAt: $project->getCreatedAt(),
            updatedAt: $project->getUpdatedAt()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'owner_id' => $this->ownerId,
            'start_date' => $this->startDate?->format('Y-m-d'),
            'end_date' => $this->endDate?->format('Y-m-d'),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
