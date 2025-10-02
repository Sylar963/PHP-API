<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class Milestone
{
    private string $id;
    private string $name;
    private string $description;
    private string $projectId;
    private DateTimeImmutable $dueDate;
    private bool $isCompleted;
    private ?DateTimeImmutable $completedAt;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $name,
        string $description,
        string $projectId,
        DateTimeImmutable $dueDate,
        bool $isCompleted = false
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->projectId = $projectId;
        $this->dueDate = $dueDate;
        $this->isCompleted = $isCompleted;
        $this->completedAt = null;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getDueDate(): DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->updateTimestamp();
    }

    public function updateDescription(string $description): void
    {
        $this->description = $description;
        $this->updateTimestamp();
    }

    public function updateDueDate(DateTimeImmutable $dueDate): void
    {
        $this->dueDate = $dueDate;
        $this->updateTimestamp();
    }

    public function markAsCompleted(): void
    {
        if (!$this->isCompleted) {
            $this->isCompleted = true;
            $this->completedAt = new DateTimeImmutable();
            $this->updateTimestamp();
        }
    }

    public function markAsIncomplete(): void
    {
        if ($this->isCompleted) {
            $this->isCompleted = false;
            $this->completedAt = null;
            $this->updateTimestamp();
        }
    }

    private function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
