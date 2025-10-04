<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\UpdateMilestoneCommand;
use App\Application\Exceptions\MilestoneNotFoundException;
use App\Domain\Entities\Milestone;
use App\Domain\Repositories\MilestoneRepositoryInterface;

class UpdateMilestoneHandler
{
    public function __construct(
        private MilestoneRepositoryInterface $milestoneRepository
    ) {
    }

    public function handle(UpdateMilestoneCommand $command): Milestone
    {
        $milestone = $this->milestoneRepository->findById($command->id);

        if (!$milestone) {
            throw new MilestoneNotFoundException("Milestone with ID {$command->id} not found");
        }

        if ($command->name !== null) {
            $milestone->updateName($command->name);
        }

        if ($command->description !== null) {
            $milestone->updateDescription($command->description);
        }

        if ($command->dueDate !== null) {
            $milestone->updateDueDate(new \DateTimeImmutable($command->dueDate));
        }

        if ($command->isCompleted !== null) {
            if ($command->isCompleted) {
                $milestone->markAsCompleted();
            } else {
                $milestone->markAsIncomplete();
            }
        }

        $this->milestoneRepository->save($milestone);

        return $milestone;
    }
}
