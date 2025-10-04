<?php

declare(strict_types=1);

namespace App\Application\DTOs;

use DateTimeImmutable;

class TimeEntryDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly string $taskId,
        public readonly string $startTime,
        public readonly ?string $endTime,
        public readonly int $duration,
        public readonly string $description,
        public readonly DateTimeImmutable $createdAt,
        public readonly DateTimeImmutable $updatedAt
    ) {
    }

    public static function fromEntity($timeEntry): self
    {
        return new self(
            id: $timeEntry->getId(),
            userId: $timeEntry->getUserId(),
            taskId: $timeEntry->getTaskId(),
            startTime: $timeEntry->getStartTime()->format('Y-m-d H:i:s'),
            endTime: $timeEntry->getEndTime()?->format('Y-m-d H:i:s'),
            duration: $timeEntry->getDuration(),
            description: $timeEntry->getDescription(),
            createdAt: $timeEntry->getCreatedAt(),
            updatedAt: $timeEntry->getUpdatedAt()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'task_id' => $this->taskId,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'duration' => $this->duration,
            'description' => $this->description,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
