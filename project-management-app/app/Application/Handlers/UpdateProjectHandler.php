<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\UpdateProjectCommand;
use App\Application\Exceptions\ProjectNotFoundException;
use App\Domain\Entities\Project;
use App\Domain\Events\ProjectUpdated;
use App\Domain\Repositories\ProjectRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class UpdateProjectHandler
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private Dispatcher $eventDispatcher
    ) {
    }

    public function handle(UpdateProjectCommand $command): Project
    {
        $project = $this->projectRepository->findById($command->projectId);

        if (!$project) {
            throw new ProjectNotFoundException("Project not found: {$command->projectId}");
        }

        $changes = [];

        if ($command->name !== null) {
            $project->updateName($command->name);
            $changes['name'] = $command->name;
        }

        if ($command->description !== null) {
            $project->updateDescription($command->description);
            $changes['description'] = $command->description;
        }

        if ($command->status !== null) {
            $project->updateStatus($command->status);
            $changes['status'] = $command->status;
        }

        if ($command->startDate !== null || $command->endDate !== null) {
            $project->setDates($command->startDate, $command->endDate);
            $changes['dates'] = true;
        }

        $this->projectRepository->save($project);

        // Dispatch domain event
        $this->eventDispatcher->dispatch(
            ProjectUpdated::create(
                $project->getId(),
                $project->getName(),
                '', // updatedBy would come from auth context
                $changes
            )
        );

        return $project;
    }
}
