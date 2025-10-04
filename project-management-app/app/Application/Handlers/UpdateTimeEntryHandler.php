<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\UpdateTimeEntryCommand;
use App\Application\Exceptions\TimeEntryNotFoundException;
use App\Domain\Entities\TimeEntry;
use App\Domain\Repositories\TimeEntryRepositoryInterface;

class UpdateTimeEntryHandler
{
    public function __construct(
        private TimeEntryRepositoryInterface $timeEntryRepository
    ) {
    }

    public function handle(UpdateTimeEntryCommand $command): TimeEntry
    {
        $timeEntry = $this->timeEntryRepository->findById($command->id);

        if (!$timeEntry) {
            throw new TimeEntryNotFoundException("TimeEntry with ID {$command->id} not found");
        }

        if ($command->description !== null) {
            $timeEntry->updateDescription($command->description);
        }

        if ($command->endTime !== null) {
            $timeEntry->stop(new \DateTimeImmutable($command->endTime));
        }

        $this->timeEntryRepository->save($timeEntry);

        return $timeEntry;
    }
}
