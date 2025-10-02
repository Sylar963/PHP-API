<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Project;
use App\Domain\Repositories\ProjectRepositoryInterface;
use App\Infrastructure\Eloquent\ProjectModel;
use DateTimeImmutable;

class EloquentProjectRepository implements ProjectRepositoryInterface
{
    public function findById(string $id): ?Project
    {
        $model = ProjectModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findByOwnerId(string $ownerId): array
    {
        return ProjectModel::where('owner_id', $ownerId)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findAll(): array
    {
        return ProjectModel::all()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function save(Project $project): void
    {
        ProjectModel::updateOrCreate(
            ['id' => $project->getId()],
            [
                'name' => $project->getName(),
                'description' => $project->getDescription(),
                'status' => $project->getStatus(),
                'owner_id' => $project->getOwnerId(),
                'start_date' => $project->getStartDate(),
                'end_date' => $project->getEndDate(),
            ]
        );
    }

    public function delete(string $id): void
    {
        ProjectModel::destroy($id);
    }

    public function findByStatus(string $status): array
    {
        return ProjectModel::where('status', $status)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function existsById(string $id): bool
    {
        return ProjectModel::where('id', $id)->exists();
    }

    private function toDomain(ProjectModel $model): Project
    {
        return new Project(
            id: $model->id,
            name: $model->name,
            description: $model->description,
            status: $model->status,
            ownerId: (string)$model->owner_id,
            startDate: $model->start_date ? new DateTimeImmutable($model->start_date->format('Y-m-d H:i:s')) : null,
            endDate: $model->end_date ? new DateTimeImmutable($model->end_date->format('Y-m-d H:i:s')) : null
        );
    }
}
