<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Team;

interface TeamRepositoryInterface
{
    public function findById(string $id): ?Team;

    public function findAll(): array;

    public function save(Team $team): void;

    public function delete(string $id): void;

    public function findByMemberId(int $userId): array;

    public function existsById(string $id): bool;

    public function addMember(string $teamId, int $userId): void;

    public function removeMember(string $teamId, int $userId): void;

    public function getMembers(string $teamId): array;

    public function findByName(string $name): ?Team;
}
