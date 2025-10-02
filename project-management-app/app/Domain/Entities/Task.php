<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class Task
{
    private string $id;
    private string $title;
    private string $description;
    private string $status;
    private string $priority;
    private string $projectId;
    private ?string $assignedTo;
    private ?DateTimeImmutable $dueDate;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $title,
        string $description,
        string $status,
        string $priority,
        string $projectId,
        ?string $assignedTo = null,
        ?DateTimeImmutable $dueDate = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
        $this->priority = $priority;
        $this->projectId = $projectId;
        $this->assignedTo = $assignedTo;
        $this->dueDate = $dueDate;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getAssignedTo(): ?string
    {
        return $this->assignedTo;
    }

    public function getDueDate(): ?DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateTitle(string $title): void
    {
        $this->title = $title;
        $this->updateTimestamp();
    }

    public function updateDescription(string $description): void
    {
        $this->description = $description;
        $this->updateTimestamp();
    }

    public function updateStatus(string $status): void
    {
        $this->status = $status;
        $this->updateTimestamp();
    }

    public function updatePriority(string $priority): void
    {
        $this->priority = $priority;
        $this->updateTimestamp();
    }

    public function assignTo(?string $userId): void
    {
        $this->assignedTo = $userId;
        $this->updateTimestamp();
    }

    public function setDueDate(?DateTimeImmutable $dueDate): void
    {
        $this->dueDate = $dueDate;
        $this->updateTimestamp();
    }

    private function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
