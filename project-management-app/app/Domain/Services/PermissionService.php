<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Entities\Project;
use App\Domain\Entities\Task;
use App\Domain\Entities\User;
use App\Domain\Repositories\ProjectRepositoryInterface;
use App\Domain\Repositories\TaskRepositoryInterface;
use App\Domain\Repositories\UserRepositoryInterface;

class PermissionService
{
    private const ROLE_HIERARCHY = [
        'super_admin' => 5,
        'project_manager' => 4,
        'team_lead' => 3,
        'team_member' => 2,
        'client' => 1,
    ];

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ProjectRepositoryInterface $projectRepository,
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    public function canCreateProject(string $userId): bool
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            return false;
        }

        return in_array($user->getRole(), ['super_admin', 'project_manager', 'team_lead'], true);
    }

    public function canUpdateProject(string $userId, string $projectId): bool
    {
        $user = $this->userRepository->findById($userId);
        $project = $this->projectRepository->findById($projectId);

        if (!$user || !$project) {
            return false;
        }

        // Super admin can update any project
        if ($user->getRole() === 'super_admin') {
            return true;
        }

        // Project owner can update their project
        if ($project->getOwnerId() === $userId) {
            return true;
        }

        // Project managers and team leads can update projects
        return in_array($user->getRole(), ['project_manager', 'team_lead'], true);
    }

    public function canDeleteProject(string $userId, string $projectId): bool
    {
        $user = $this->userRepository->findById($userId);
        $project = $this->projectRepository->findById($projectId);

        if (!$user || !$project) {
            return false;
        }

        // Only super admin and project owner can delete
        return $user->getRole() === 'super_admin' || $project->getOwnerId() === $userId;
    }

    public function canManageTask(string $userId, string $taskId): bool
    {
        $user = $this->userRepository->findById($userId);
        $task = $this->taskRepository->findById($taskId);

        if (!$user || !$task) {
            return false;
        }

        // Super admin can manage any task
        if ($user->getRole() === 'super_admin') {
            return true;
        }

        $project = $this->projectRepository->findById($task->getProjectId());

        // Project owner can manage all tasks in their project
        if ($project && $project->getOwnerId() === $userId) {
            return true;
        }

        // Task assignee can manage their own task
        if ($task->getAssignedTo() === $userId) {
            return true;
        }

        // Project managers and team leads can manage tasks
        return in_array($user->getRole(), ['project_manager', 'team_lead'], true);
    }

    public function canAssignTask(string $userId): bool
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            return false;
        }

        return in_array($user->getRole(), ['super_admin', 'project_manager', 'team_lead'], true);
    }

    public function hasHigherRole(string $userId, string $compareToUserId): bool
    {
        $user = $this->userRepository->findById($userId);
        $compareToUser = $this->userRepository->findById($compareToUserId);

        if (!$user || !$compareToUser) {
            return false;
        }

        $userLevel = self::ROLE_HIERARCHY[$user->getRole()] ?? 0;
        $compareToLevel = self::ROLE_HIERARCHY[$compareToUser->getRole()] ?? 0;

        return $userLevel > $compareToLevel;
    }

    public function canViewProject(string $userId, string $projectId): bool
    {
        $user = $this->userRepository->findById($userId);
        $project = $this->projectRepository->findById($projectId);

        if (!$user || !$project) {
            return false;
        }

        // Super admin can view everything
        if ($user->getRole() === 'super_admin') {
            return true;
        }

        // Project owner can view their project
        if ($project->getOwnerId() === $userId) {
            return true;
        }

        // Check if user has any task in the project
        $projectTasks = $this->taskRepository->findByProjectId($projectId);
        foreach ($projectTasks as $task) {
            if ($task->getAssignedTo() === $userId) {
                return true;
            }
        }

        return false;
    }
}
