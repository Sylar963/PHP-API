<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\TimeEntry;
use App\Domain\Repositories\TimeEntryRepositoryInterface;
use App\Infrastructure\Eloquent\TimeEntryModel;

class EloquentTimeEntryRepository implements TimeEntryRepositoryInterface
{
    public function findById(string $id): ?TimeEntry
    {
        $model = TimeEntryModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findByUserId(string $userId): array
    {
        return TimeEntryModel::where('user_id', (int) $userId)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findByTaskId(string $taskId): array
    {
        return TimeEntryModel::where('task_id', $taskId)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findAll(): array
    {
        return TimeEntryModel::all()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function save(TimeEntry $timeEntry): void
    {
        TimeEntryModel::updateOrCreate(
            ['id' => $timeEntry->getId()],
            [
                'user_id' => (int) $timeEntry->getUserId(),
                'task_id' => $timeEntry->getTaskId(),
                'start_time' => $timeEntry->getStartTime()->format('Y-m-d H:i:s'),
                'end_time' => $timeEntry->getEndTime()?->format('Y-m-d H:i:s'),
                'duration' => $timeEntry->getDuration(),
                'description' => $timeEntry->getDescription(),
            ]
        );
    }

    public function delete(string $id): void
    {
        TimeEntryModel::destroy($id);
    }

    public function findActiveTimeEntry(string $userId): ?TimeEntry
    {
        $model = TimeEntryModel::where('user_id', (int) $userId)
            ->whereNull('end_time')
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function getTotalTimeByTask(string $taskId): int
    {
        return (int) TimeEntryModel::where('task_id', $taskId)
            ->sum('duration');
    }

    public function getTotalTimeByUser(string $userId): int
    {
        return (int) TimeEntryModel::where('user_id', (int) $userId)
            ->sum('duration');
    }

    private function toDomain(TimeEntryModel $model): TimeEntry
    {
        $timeEntry = new TimeEntry(
            id: $model->id,
            userId: (string) $model->user_id,
            taskId: $model->task_id,
            startTime: new \DateTimeImmutable($model->start_time),
            description: $model->description ?? '',
            endTime: $model->end_time ? new \DateTimeImmutable($model->end_time) : null
        );

        // Use reflection to set timestamps from database
        $reflection = new \ReflectionClass($timeEntry);

        $createdAtProperty = $reflection->getProperty('createdAt');
        $createdAtProperty->setAccessible(true);
        $createdAtProperty->setValue($timeEntry, new \DateTimeImmutable($model->created_at));

        $updatedAtProperty = $reflection->getProperty('updatedAt');
        $updatedAtProperty->setAccessible(true);
        $updatedAtProperty->setValue($timeEntry, new \DateTimeImmutable($model->updated_at));

        return $timeEntry;
    }
}
