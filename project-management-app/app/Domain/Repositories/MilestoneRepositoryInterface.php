<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Milestone;

interface MilestoneRepositoryInterface
{
    public function findById(string $id): ?Milestone;

    public function findByProjectId(string $projectId): array;

    public function findAll(): array;

    public function save(Milestone $milestone): void;

    public function delete(string $id): void;

    public function findCompletedMilestones(string $projectId): array;

    public function findUpcomingMilestones(string $projectId): array;
}
