<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function findById(string $id): ?User;

    public function findByEmail(string $email): ?User;

    public function findAll(): array;

    public function save(User $user): void;

    public function delete(string $id): void;

    public function findByRole(string $role): array;

    public function findActiveUsers(): array;

    public function existsByEmail(string $email): bool;
}
