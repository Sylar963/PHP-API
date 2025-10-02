<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Commands\CreateProjectCommand;
use App\Application\Commands\UpdateProjectCommand;
use App\Application\DTOs\ProjectDTO;
use App\Application\Handlers\CreateProjectHandler;
use App\Application\Handlers\UpdateProjectHandler;
use App\Domain\Repositories\ProjectRepositoryInterface;
use App\Domain\Services\PermissionService;

class ProjectManagementService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private CreateProjectHandler $createProjectHandler,
        private UpdateProjectHandler $updateProjectHandler,
        private PermissionService $permissionService
    ) {
    }

    public function createProject(CreateProjectCommand $command): ProjectDTO
    {
        $project = $this->createProjectHandler->handle($command);
        return ProjectDTO::fromEntity($project);
    }

    public function updateProject(UpdateProjectCommand $command, string $userId): ProjectDTO
    {
        if (!$this->permissionService->canUpdateProject($userId, $command->projectId)) {
            throw new \App\Application\Exceptions\UnauthorizedException(
                "User does not have permission to update this project"
            );
        }

        $project = $this->updateProjectHandler->handle($command);
        return ProjectDTO::fromEntity($project);
    }

    public function getProject(string $projectId, string $userId): ProjectDTO
    {
        if (!$this->permissionService->canViewProject($userId, $projectId)) {
            throw new \App\Application\Exceptions\UnauthorizedException(
                "User does not have permission to view this project"
            );
        }

        $project = $this->projectRepository->findById($projectId);

        if (!$project) {
            throw new \App\Application\Exceptions\ProjectNotFoundException();
        }

        return ProjectDTO::fromEntity($project);
    }

    public function getAllProjects(): array
    {
        $projects = $this->projectRepository->findAll();
        return array_map(
            fn($project) => ProjectDTO::fromEntity($project),
            $projects
        );
    }

    public function getUserProjects(string $userId): array
    {
        $projects = $this->projectRepository->findByOwnerId($userId);
        return array_map(
            fn($project) => ProjectDTO::fromEntity($project),
            $projects
        );
    }

    public function deleteProject(string $projectId, string $userId): void
    {
        if (!$this->permissionService->canDeleteProject($userId, $projectId)) {
            throw new \App\Application\Exceptions\UnauthorizedException(
                "User does not have permission to delete this project"
            );
        }

        $this->projectRepository->delete($projectId);
    }
}
