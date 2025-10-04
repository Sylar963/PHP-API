<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\UpdateTeamCommand;
use App\Application\Exceptions\TeamNotFoundException;
use App\Domain\Entities\Team;
use App\Domain\Repositories\TeamRepositoryInterface;

class UpdateTeamHandler
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository
    ) {
    }

    public function handle(UpdateTeamCommand $command): Team
    {
        $team = $this->teamRepository->findById($command->id);

        if (!$team) {
            throw new TeamNotFoundException("Team with ID {$command->id} not found");
        }

        if ($command->name !== null) {
            $team->updateName($command->name);
        }

        if ($command->description !== null) {
            $team->updateDescription($command->description);
        }

        $this->teamRepository->save($team);

        return $team;
    }
}
