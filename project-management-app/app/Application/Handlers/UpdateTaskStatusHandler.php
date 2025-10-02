<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\UpdateTaskStatusCommand;
use App\Application\Exceptions\TaskNotFoundException;
use App\Domain\Entities\Task;
use App\Domain\Events\TaskStatusChanged;
use App\Domain\Repositories\TaskRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class UpdateTaskStatusHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private Dispatcher $eventDispatcher
    ) {
    }

    public function handle(UpdateTaskStatusCommand $command): Task
    {
        $task = $this->taskRepository->findById($command->taskId);

        if (!$task) {
            throw new TaskNotFoundException("Task not found: {$command->taskId}");
        }

        $oldStatus = $task->getStatus();
        $task->updateStatus($command->newStatus);
        $this->taskRepository->save($task);

        // Dispatch domain event
        $this->eventDispatcher->dispatch(
            TaskStatusChanged::create(
                $task->getId(),
                $task->getTitle(),
                $oldStatus,
                $command->newStatus,
                $command->updatedBy
            )
        );

        return $task;
    }
}
