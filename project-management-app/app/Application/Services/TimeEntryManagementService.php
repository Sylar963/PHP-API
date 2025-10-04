<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Commands\CreateTimeEntryCommand;
use App\Application\Commands\UpdateTimeEntryCommand;
use App\Application\Handlers\CreateTimeEntryHandler;
use App\Application\Handlers\UpdateTimeEntryHandler;
use App\Application\DTOs\TimeEntryDTO;
use App\Domain\Repositories\TimeEntryRepositoryInterface;

class TimeEntryManagementService
{
    public function __construct(
        private TimeEntryRepositoryInterface $timeEntryRepository,
        private CreateTimeEntryHandler $createTimeEntryHandler,
        private UpdateTimeEntryHandler $updateTimeEntryHandler
    ) {
    }

    public function createTimeEntry(CreateTimeEntryCommand $command): TimeEntryDTO
    {
        $timeEntry = $this->createTimeEntryHandler->handle($command);
        return TimeEntryDTO::fromEntity($timeEntry);
    }

    public function updateTimeEntry(UpdateTimeEntryCommand $command): TimeEntryDTO
    {
        $timeEntry = $this->updateTimeEntryHandler->handle($command);
        return TimeEntryDTO::fromEntity($timeEntry);
    }

    public function getTimeEntry(string $id): TimeEntryDTO
    {
        $timeEntry = $this->timeEntryRepository->findById($id);

        if (!$timeEntry) {
            throw new \App\Application\Exceptions\TimeEntryNotFoundException("TimeEntry with ID {$id} not found");
        }

        return TimeEntryDTO::fromEntity($timeEntry);
    }

    public function getAllTimeEntries(?string $taskId = null): array
    {
        if ($taskId) {
            $timeEntries = $this->timeEntryRepository->findByTaskId($taskId);
        } else {
            $timeEntries = $this->timeEntryRepository->findAll();
        }

        return array_map(fn($timeEntry) => TimeEntryDTO::fromEntity($timeEntry), $timeEntries);
    }

    public function deleteTimeEntry(string $id): void
    {
        $timeEntry = $this->timeEntryRepository->findById($id);

        if (!$timeEntry) {
            throw new \App\Application\Exceptions\TimeEntryNotFoundException("TimeEntry with ID {$id} not found");
        }

        $this->timeEntryRepository->delete($id);
    }
}
