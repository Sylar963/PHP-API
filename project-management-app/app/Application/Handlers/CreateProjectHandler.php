<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\CreateProjectCommand;
use App\Domain\Entities\Project;
use App\Domain\Events\ProjectCreated;
use App\Domain\Repositories\ProjectRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Contracts\Events\Dispatcher;

class CreateProjectHandler
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private Dispatcher $eventDispatcher
    ) {
    }

    public function handle(CreateProjectCommand $command): Project
    {
        $project = new Project(
            id: Str::uuid()->toString(),
            name: $command->name,
            description: $command->description,
            status: $command->status,
            ownerId: $command->ownerId,
            startDate: $command->startDate,
            endDate: $command->endDate
        );

        $this->projectRepository->save($project);

        // Dispatch domain event
        $this->eventDispatcher->dispatch(
            ProjectCreated::create(
                $project->getId(),
                $project->getName(),
                $project->getOwnerId()
            )
        );

        return $project;
    }
}
