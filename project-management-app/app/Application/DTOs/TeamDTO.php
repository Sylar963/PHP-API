<?php

declare(strict_types=1);

namespace App\Application\DTOs;

use DateTimeImmutable;

class TeamDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly array $memberIds,
        public readonly DateTimeImmutable $createdAt,
        public readonly DateTimeImmutable $updatedAt
    ) {
    }

    public static function fromEntity($team): self
    {
        return new self(
            id: $team->getId(),
            name: $team->getName(),
            description: $team->getDescription(),
            memberIds: $team->getMemberIds(),
            createdAt: $team->getCreatedAt(),
            updatedAt: $team->getUpdatedAt()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'member_ids' => $this->memberIds,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
