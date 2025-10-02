<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class User
{
    private string $id;
    private string $name;
    private string $email;
    private string $role;
    private bool $isActive;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $name,
        string $email,
        string $role,
        bool $isActive = true
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
        $this->isActive = $isActive;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function isActive(): bool
    {
        return $this->isActive;
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

    public function updateEmail(string $email): void
    {
        $this->email = $email;
        $this->updateTimestamp();
    }

    public function changeRole(string $role): void
    {
        $this->role = $role;
        $this->updateTimestamp();
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updateTimestamp();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updateTimestamp();
    }

    private function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
