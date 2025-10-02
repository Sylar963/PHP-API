<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class Team
{
    private string $id;
    private string $name;
    private string $description;
    private array $memberIds;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $name,
        string $description,
        array $memberIds = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->memberIds = $memberIds;
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

    public function getMemberIds(): array
    {
        return $this->memberIds;
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

    public function addMember(string $userId): void
    {
        if (!in_array($userId, $this->memberIds, true)) {
            $this->memberIds[] = $userId;
            $this->updateTimestamp();
        }
    }

    public function removeMember(string $userId): void
    {
        $this->memberIds = array_values(array_filter(
            $this->memberIds,
            fn($id) => $id !== $userId
        ));
        $this->updateTimestamp();
    }

    public function hasMember(string $userId): bool
    {
        return in_array($userId, $this->memberIds, true);
    }

    private function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
