<?php

declare(strict_types=1);

namespace App\Application\DTOs;

use DateTimeImmutable;

class MilestoneDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $projectId,
        public readonly string $name,
        public readonly string $description,
        public readonly string $dueDate,
        public readonly bool $isCompleted,
        public readonly ?string $completedAt,
        public readonly DateTimeImmutable $createdAt,
        public readonly DateTimeImmutable $updatedAt
    ) {
    }

    public static function fromEntity($milestone): self
    {
        return new self(
            id: $milestone->getId(),
            projectId: $milestone->getProjectId(),
            name: $milestone->getName(),
            description: $milestone->getDescription(),
            dueDate: $milestone->getDueDate()->format('Y-m-d'),
            isCompleted: $milestone->isCompleted(),
            completedAt: $milestone->getCompletedAt()?->format('Y-m-d H:i:s'),
            createdAt: $milestone->getCreatedAt(),
            updatedAt: $milestone->getUpdatedAt()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->projectId,
            'name' => $this->name,
            'description' => $this->description,
            'due_date' => $this->dueDate,
            'is_completed' => $this->isCompleted,
            'completed_at' => $this->completedAt,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
