<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\CreateMilestoneCommand;
use App\Domain\Entities\Milestone;
use App\Domain\Repositories\MilestoneRepositoryInterface;
use Ramsey\Uuid\Uuid;

class CreateMilestoneHandler
{
    public function __construct(
        private MilestoneRepositoryInterface $milestoneRepository
    ) {
    }

    public function handle(CreateMilestoneCommand $command): Milestone
    {
        $milestone = new Milestone(
            id: Uuid::uuid4()->toString(),
            name: $command->name,
            description: $command->description,
            projectId: $command->projectId,
            dueDate: new \DateTimeImmutable($command->dueDate),
            isCompleted: $command->isCompleted
        );

        $this->milestoneRepository->save($milestone);

        return $milestone;
    }
}
