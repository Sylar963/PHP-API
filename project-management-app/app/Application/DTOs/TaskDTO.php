<?php

declare(strict_types=1);

namespace App\Application\DTOs;

use DateTimeImmutable;

class TaskDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $description,
        public readonly string $status,
        public readonly string $priority,
        public readonly string $projectId,
        public readonly ?string $assignedTo,
        public readonly ?DateTimeImmutable $dueDate,
        public readonly DateTimeImmutable $createdAt,
        public readonly DateTimeImmutable $updatedAt
    ) {
    }

    public static function fromEntity($task): self
    {
        return new self(
            id: $task->getId(),
            title: $task->getTitle(),
            description: $task->getDescription(),
            status: $task->getStatus(),
            priority: $task->getPriority(),
            projectId: $task->getProjectId(),
            assignedTo: $task->getAssignedTo(),
            dueDate: $task->getDueDate(),
            createdAt: $task->getCreatedAt(),
            updatedAt: $task->getUpdatedAt()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'project_id' => $this->projectId,
            'assigned_to' => $this->assignedTo,
            'due_date' => $this->dueDate?->format('Y-m-d'),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
