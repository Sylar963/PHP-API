<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\CreateTeamCommand;
use App\Domain\Entities\Team;
use App\Infrastructure\Eloquent\TeamModel;

class CreateTeamHandler
{
    public function handle(CreateTeamCommand $command): Team
    {
        // Create team in database
        $teamModel = TeamModel::create([
            'name' => $command->name,
            'description' => $command->description,
        ]);

        // Convert to domain entity
        return new Team(
            id: $teamModel->id,
            name: $teamModel->name,
            description: $teamModel->description
        );
    }
}
