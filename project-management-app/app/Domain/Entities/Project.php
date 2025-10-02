<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class Project
{
    private string $id;
    private string $name;
    private string $description;
    private string $status;
    private string $ownerId;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    private ?DateTimeImmutable $startDate;
    private ?DateTimeImmutable $endDate;

    public function __construct(
        string $id,
        string $name,
        string $description,
        string $status,
        string $ownerId,
        ?DateTimeImmutable $startDate = null,
        ?DateTimeImmutable $endDate = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
        $this->ownerId = $ownerId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
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

    public function updateStatus(string $status): void
    {
        $this->status = $status;
        $this->updateTimestamp();
    }

    public function setDates(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): void
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->updateTimestamp();
    }

    private function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
