<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Commands\CreateMilestoneCommand;
use App\Application\Commands\UpdateMilestoneCommand;
use App\Application\Handlers\CreateMilestoneHandler;
use App\Application\Handlers\UpdateMilestoneHandler;
use App\Application\DTOs\MilestoneDTO;
use App\Domain\Repositories\MilestoneRepositoryInterface;

class MilestoneManagementService
{
    public function __construct(
        private MilestoneRepositoryInterface $milestoneRepository,
        private CreateMilestoneHandler $createMilestoneHandler,
        private UpdateMilestoneHandler $updateMilestoneHandler
    ) {
    }

    public function createMilestone(CreateMilestoneCommand $command): MilestoneDTO
    {
        $milestone = $this->createMilestoneHandler->handle($command);
        return MilestoneDTO::fromEntity($milestone);
    }

    public function updateMilestone(UpdateMilestoneCommand $command): MilestoneDTO
    {
        $milestone = $this->updateMilestoneHandler->handle($command);
        return MilestoneDTO::fromEntity($milestone);
    }

    public function getMilestone(string $id): MilestoneDTO
    {
        $milestone = $this->milestoneRepository->findById($id);

        if (!$milestone) {
            throw new \App\Application\Exceptions\MilestoneNotFoundException("Milestone with ID {$id} not found");
        }

        return MilestoneDTO::fromEntity($milestone);
    }

    public function getAllMilestones(?string $projectId = null): array
    {
        if ($projectId) {
            $milestones = $this->milestoneRepository->findByProjectId($projectId);
        } else {
            $milestones = $this->milestoneRepository->findAll();
        }

        return array_map(fn($milestone) => MilestoneDTO::fromEntity($milestone), $milestones);
    }

    public function deleteMilestone(string $id): void
    {
        $milestone = $this->milestoneRepository->findById($id);

        if (!$milestone) {
            throw new \App\Application\Exceptions\MilestoneNotFoundException("Milestone with ID {$id} not found");
        }

        $this->milestoneRepository->delete($id);
    }
}
