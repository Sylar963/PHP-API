<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Milestone;
use App\Domain\Repositories\MilestoneRepositoryInterface;
use App\Infrastructure\Eloquent\MilestoneModel;

class EloquentMilestoneRepository implements MilestoneRepositoryInterface
{
    public function findById(string $id): ?Milestone
    {
        $model = MilestoneModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(): array
    {
        return MilestoneModel::all()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findByProjectId(string $projectId): array
    {
        return MilestoneModel::where('project_id', $projectId)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function save(Milestone $milestone): void
    {
        MilestoneModel::updateOrCreate(
            ['id' => $milestone->getId()],
            [
                'project_id' => $milestone->getProjectId(),
                'name' => $milestone->getName(),
                'description' => $milestone->getDescription(),
                'due_date' => $milestone->getDueDate()->format('Y-m-d'),
                'is_completed' => $milestone->isCompleted(),
                'completed_at' => $milestone->getCompletedAt()?->format('Y-m-d H:i:s'),
            ]
        );
    }

    public function delete(string $id): void
    {
        MilestoneModel::destroy($id);
    }

    public function findCompletedMilestones(string $projectId): array
    {
        return MilestoneModel::where('project_id', $projectId)
            ->where('is_completed', true)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findUpcomingMilestones(string $projectId): array
    {
        return MilestoneModel::where('project_id', $projectId)
            ->where('is_completed', false)
            ->where('due_date', '>=', now())
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    private function toDomain(MilestoneModel $model): Milestone
    {
        $milestone = new Milestone(
            id: $model->id,
            name: $model->name,
            description: $model->description,
            projectId: $model->project_id,
            dueDate: new \DateTimeImmutable($model->due_date),
            isCompleted: (bool) $model->is_completed
        );

        // Use reflection to set timestamps from database
        $reflection = new \ReflectionClass($milestone);

        $createdAtProperty = $reflection->getProperty('createdAt');
        $createdAtProperty->setAccessible(true);
        $createdAtProperty->setValue($milestone, new \DateTimeImmutable($model->created_at));

        $updatedAtProperty = $reflection->getProperty('updatedAt');
        $updatedAtProperty->setAccessible(true);
        $updatedAtProperty->setValue($milestone, new \DateTimeImmutable($model->updated_at));

        if ($model->completed_at) {
            $completedAtProperty = $reflection->getProperty('completedAt');
            $completedAtProperty->setAccessible(true);
            $completedAtProperty->setValue($milestone, new \DateTimeImmutable($model->completed_at));
        }

        return $milestone;
    }
}
