<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class TimeEntry
{
    private string $id;
    private string $userId;
    private string $taskId;
    private DateTimeImmutable $startTime;
    private ?DateTimeImmutable $endTime;
    private int $duration; // in minutes
    private string $description;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $userId,
        string $taskId,
        DateTimeImmutable $startTime,
        string $description = '',
        ?DateTimeImmutable $endTime = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->taskId = $taskId;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->description = $description;
        $this->duration = $this->calculateDuration();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getStartTime(): DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): ?DateTimeImmutable
    {
        return $this->endTime;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function stop(DateTimeImmutable $endTime): void
    {
        if ($this->endTime === null) {
            $this->endTime = $endTime;
            $this->duration = $this->calculateDuration();
            $this->updateTimestamp();
        }
    }

    public function updateDescription(string $description): void
    {
        $this->description = $description;
        $this->updateTimestamp();
    }

    private function calculateDuration(): int
    {
        if ($this->endTime === null) {
            return 0;
        }

        $interval = $this->startTime->diff($this->endTime);
        return ($interval->h * 60) + $interval->i;
    }

    private function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
