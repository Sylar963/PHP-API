<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Project;

interface ProjectRepositoryInterface
{
    public function findById(string $id): ?Project;

    public function findByOwnerId(string $ownerId): array;

    public function findAll(): array;

    public function save(Project $project): void;

    public function delete(string $id): void;

    public function findByStatus(string $status): array;

    public function existsById(string $id): bool;
}
