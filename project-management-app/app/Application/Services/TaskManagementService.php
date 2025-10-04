<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Commands\AssignTaskCommand;
use App\Application\Commands\CreateTaskCommand;
use App\Application\Commands\UpdateTaskCommand;
use App\Application\Commands\UpdateTaskStatusCommand;
use App\Application\DTOs\TaskDTO;
use App\Application\Handlers\AssignTaskHandler;
use App\Application\Handlers\CreateTaskHandler;
use App\Application\Handlers\UpdateTaskHandler;
use App\Application\Handlers\UpdateTaskStatusHandler;
use App\Domain\Repositories\TaskRepositoryInterface;
use App\Domain\Services\PermissionService;

class TaskManagementService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private CreateTaskHandler $createTaskHandler,
        private AssignTaskHandler $assignTaskHandler,
        private UpdateTaskHandler $updateTaskHandler,
        private UpdateTaskStatusHandler $updateTaskStatusHandler,
        private PermissionService $permissionService
    ) {
    }

    public function createTask(CreateTaskCommand $command): TaskDTO
    {
        $task = $this->createTaskHandler->handle($command);
        return TaskDTO::fromEntity($task);
    }

    public function assignTask(AssignTaskCommand $command, string $userId): TaskDTO
    {
        if (!$this->permissionService->canAssignTask($userId)) {
            throw new \App\Application\Exceptions\UnauthorizedException(
                "User does not have permission to assign tasks"
            );
        }

        $task = $this->assignTaskHandler->handle($command);
        return TaskDTO::fromEntity($task);
    }

    public function updateTask(UpdateTaskCommand $command, string $userId): TaskDTO
    {
        if (!$this->permissionService->canManageTask($userId, $command->taskId)) {
            throw new \App\Application\Exceptions\UnauthorizedException(
                "User does not have permission to update this task"
            );
        }

        $task = $this->updateTaskHandler->handle($command);
        return TaskDTO::fromEntity($task);
    }

    public function updateTaskStatus(UpdateTaskStatusCommand $command, string $userId): TaskDTO
    {
        if (!$this->permissionService->canManageTask($userId, $command->taskId)) {
            throw new \App\Application\Exceptions\UnauthorizedException(
                "User does not have permission to update this task"
            );
        }

        $task = $this->updateTaskStatusHandler->handle($command);
        return TaskDTO::fromEntity($task);
    }

    public function getTask(string $taskId): TaskDTO
    {
        $task = $this->taskRepository->findById($taskId);

        if (!$task) {
            throw new \App\Application\Exceptions\TaskNotFoundException();
        }

        return TaskDTO::fromEntity($task);
    }

    public function getProjectTasks(string $projectId): array
    {
        $tasks = $this->taskRepository->findByProjectId($projectId);
        return array_map(
            fn($task) => TaskDTO::fromEntity($task),
            $tasks
        );
    }

    public function getUserTasks(string $userId): array
    {
        $tasks = $this->taskRepository->findByAssignedUser($userId);
        return array_map(
            fn($task) => TaskDTO::fromEntity($task),
            $tasks
        );
    }

    public function deleteTask(string $taskId, string $userId): void
    {
        if (!$this->permissionService->canManageTask($userId, $taskId)) {
            throw new \App\Application\Exceptions\UnauthorizedException(
                "User does not have permission to delete this task"
            );
        }

        $this->taskRepository->delete($taskId);
    }
}
