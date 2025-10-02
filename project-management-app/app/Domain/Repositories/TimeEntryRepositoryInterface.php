<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\TimeEntry;

interface TimeEntryRepositoryInterface
{
    public function findById(string $id): ?TimeEntry;

    public function findByUserId(string $userId): array;

    public function findByTaskId(string $taskId): array;

    public function findAll(): array;

    public function save(TimeEntry $timeEntry): void;

    public function delete(string $id): void;

    public function findActiveTimeEntry(string $userId): ?TimeEntry;

    public function getTotalTimeByTask(string $taskId): int;

    public function getTotalTimeByUser(string $userId): int;
}
