<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Task;
use App\Domain\Repositories\TaskRepositoryInterface;
use App\Infrastructure\Eloquent\TaskModel;
use DateTimeImmutable;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function findById(string $id): ?Task
    {
        $model = TaskModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findByProjectId(string $projectId): array
    {
        return TaskModel::where('project_id', $projectId)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findByAssignedUser(string $userId): array
    {
        return TaskModel::where('assigned_to', $userId)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findAll(): array
    {
        return TaskModel::all()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function save(Task $task): void
    {
        TaskModel::updateOrCreate(
            ['id' => $task->getId()],
            [
                'title' => $task->getTitle(),
                'description' => $task->getDescription(),
                'status' => $task->getStatus(),
                'priority' => $task->getPriority(),
                'project_id' => $task->getProjectId(),
                'assigned_to' => $task->getAssignedTo(),
                'due_date' => $task->getDueDate(),
            ]
        );
    }

    public function delete(string $id): void
    {
        TaskModel::destroy($id);
    }

    public function findByStatus(string $status): array
    {
        return TaskModel::where('status', $status)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findByPriority(string $priority): array
    {
        return TaskModel::where('priority', $priority)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findOverdueTasks(): array
    {
        return TaskModel::where('due_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    private function toDomain(TaskModel $model): Task
    {
        return new Task(
            id: $model->id,
            title: $model->title,
            description: $model->description,
            status: $model->status,
            priority: $model->priority,
            projectId: $model->project_id,
            assignedTo: $model->assigned_to,
            dueDate: $model->due_date ? new DateTimeImmutable($model->due_date) : null
        );
    }
}
