<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Commands\CreateTeamCommand;
use App\Application\Handlers\CreateTeamHandler;
use App\Application\DTOs\TeamDTO;
use App\Domain\Repositories\TeamRepositoryInterface;

class TeamManagementService
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private CreateTeamHandler $createTeamHandler
    ) {
    }

    public function createTeam(CreateTeamCommand $command): TeamDTO
    {
        $team = $this->createTeamHandler->handle($command);
        return TeamDTO::fromEntity($team);
    }

    public function getTeam(string $id): TeamDTO
    {
        $team = $this->teamRepository->findById($id);

        if (!$team) {
            throw new \App\Application\Exceptions\TeamNotFoundException("Team with ID {$id} not found");
        }

        return TeamDTO::fromEntity($team);
    }

    public function getAllTeams(): array
    {
        $teams = $this->teamRepository->findAll();
        return array_map(fn($team) => TeamDTO::fromEntity($team), $teams);
    }

    public function deleteTeam(string $id): void
    {
        $team = $this->teamRepository->findById($id);

        if (!$team) {
            throw new \App\Application\Exceptions\TeamNotFoundException("Team with ID {$id} not found");
        }

        $this->teamRepository->delete($id);
    }

    public function addMember(string $teamId, int $userId): void
    {
        $this->teamRepository->addMember($teamId, $userId);
    }

    public function removeMember(string $teamId, int $userId): void
    {
        $this->teamRepository->removeMember($teamId, $userId);
    }

    public function getMembers(string $teamId): array
    {
        return $this->teamRepository->getMembers($teamId);
    }
}
