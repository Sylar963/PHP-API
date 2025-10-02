<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Task;

interface TaskRepositoryInterface
{
    public function findById(string $id): ?Task;

    public function findByProjectId(string $projectId): array;

    public function findByAssignedUser(string $userId): array;

    public function findAll(): array;

    public function save(Task $task): void;

    public function delete(string $id): void;

    public function findByStatus(string $status): array;

    public function findByPriority(string $priority): array;

    public function findOverdueTasks(): array;
}
