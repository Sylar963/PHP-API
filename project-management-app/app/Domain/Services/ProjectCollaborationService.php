<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Entities\Project;
use App\Domain\Entities\Task;
use App\Domain\Entities\User;
use App\Domain\Repositories\ProjectRepositoryInterface;
use App\Domain\Repositories\TaskRepositoryInterface;
use App\Domain\Repositories\UserRepositoryInterface;

class ProjectCollaborationService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private TaskRepositoryInterface $taskRepository,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function assignUserToProject(string $projectId, string $userId): bool
    {
        $project = $this->projectRepository->findById($projectId);
        $user = $this->userRepository->findById($userId);

        if (!$project || !$user) {
            return false;
        }

        // Business logic for assigning user to project
        // This would involve creating a pivot record in infrastructure layer
        return true;
    }

    public function canUserAccessProject(string $userId, string $projectId): bool
    {
        $project = $this->projectRepository->findById($projectId);
        $user = $this->userRepository->findById($userId);

        if (!$project || !$user) {
            return false;
        }

        // Super admin and project owner always have access
        if ($user->getRole() === 'super_admin' || $project->getOwnerId() === $userId) {
            return true;
        }

        // Check if user is assigned to any task in the project
        $projectTasks = $this->taskRepository->findByProjectId($projectId);
        foreach ($projectTasks as $task) {
            if ($task->getAssignedTo() === $userId) {
                return true;
            }
        }

        return false;
    }

    public function getProjectCollaborators(string $projectId): array
    {
        $collaborators = [];
        $tasks = $this->taskRepository->findByProjectId($projectId);

        foreach ($tasks as $task) {
            $assignedUserId = $task->getAssignedTo();
            if ($assignedUserId && !in_array($assignedUserId, $collaborators, true)) {
                $collaborators[] = $assignedUserId;
            }
        }

        return array_map(
            fn($userId) => $this->userRepository->findById($userId),
            $collaborators
        );
    }

    public function getProjectProgress(string $projectId): float
    {
        $tasks = $this->taskRepository->findByProjectId($projectId);

        if (empty($tasks)) {
            return 0.0;
        }

        $completedTasks = array_filter(
            $tasks,
            fn($task) => $task->getStatus() === 'completed'
        );

        return (count($completedTasks) / count($tasks)) * 100;
    }
}
