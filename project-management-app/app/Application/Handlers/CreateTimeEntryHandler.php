<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\CreateTimeEntryCommand;
use App\Domain\Entities\TimeEntry;
use App\Domain\Repositories\TimeEntryRepositoryInterface;
use Ramsey\Uuid\Uuid;

class CreateTimeEntryHandler
{
    public function __construct(
        private TimeEntryRepositoryInterface $timeEntryRepository
    ) {
    }

    public function handle(CreateTimeEntryCommand $command): TimeEntry
    {
        $timeEntry = new TimeEntry(
            id: Uuid::uuid4()->toString(),
            userId: $command->userId,
            taskId: $command->taskId,
            startTime: new \DateTimeImmutable($command->startTime),
            description: $command->description,
            endTime: $command->endTime ? new \DateTimeImmutable($command->endTime) : null
        );

        $this->timeEntryRepository->save($timeEntry);

        return $timeEntry;
    }
}
