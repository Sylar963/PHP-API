<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Entities\TimeEntry;
use App\Domain\Repositories\TimeEntryRepositoryInterface;
use App\Domain\Repositories\TaskRepositoryInterface;
use DateTimeImmutable;

class TimeTrackingService
{
    public function __construct(
        private TimeEntryRepositoryInterface $timeEntryRepository,
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    public function startTimeTracking(
        string $userId,
        string $taskId,
        string $description = ''
    ): ?TimeEntry {
        // Check if user already has an active time entry
        $activeEntry = $this->timeEntryRepository->findActiveTimeEntry($userId);
        if ($activeEntry) {
            return null; // User must stop current timer first
        }

        // Check if task exists
        $task = $this->taskRepository->findById($taskId);
        if (!$task) {
            return null;
        }

        $timeEntry = new TimeEntry(
            id: $this->generateId(),
            userId: $userId,
            taskId: $taskId,
            startTime: new DateTimeImmutable(),
            description: $description
        );

        $this->timeEntryRepository->save($timeEntry);

        return $timeEntry;
    }

    public function stopTimeTracking(string $userId): ?TimeEntry
    {
        $activeEntry = $this->timeEntryRepository->findActiveTimeEntry($userId);

        if (!$activeEntry) {
            return null;
        }

        $activeEntry->stop(new DateTimeImmutable());
        $this->timeEntryRepository->save($activeEntry);

        return $activeEntry;
    }

    public function getTaskTotalTime(string $taskId): int
    {
        return $this->timeEntryRepository->getTotalTimeByTask($taskId);
    }

    public function getUserTotalTime(string $userId): int
    {
        return $this->timeEntryRepository->getTotalTimeByUser($userId);
    }

    public function getUserTimeEntriesByDateRange(
        string $userId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): array {
        $allEntries = $this->timeEntryRepository->findByUserId($userId);

        return array_filter(
            $allEntries,
            fn($entry) => $entry->getStartTime() >= $startDate
                && $entry->getStartTime() <= $endDate
        );
    }

    private function generateId(): string
    {
        return uniqid('time_', true);
    }
}
