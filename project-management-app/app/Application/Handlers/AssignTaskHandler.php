<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\AssignTaskCommand;
use App\Application\Exceptions\TaskNotFoundException;
use App\Domain\Entities\Task;
use App\Domain\Events\TaskAssigned;
use App\Domain\Repositories\TaskRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class AssignTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private Dispatcher $eventDispatcher
    ) {
    }

    public function handle(AssignTaskCommand $command): Task
    {
        $task = $this->taskRepository->findById($command->taskId);

        if (!$task) {
            throw new TaskNotFoundException("Task not found: {$command->taskId}");
        }

        $task->assignTo($command->userId);
        $this->taskRepository->save($task);

        // Dispatch domain event
        $this->eventDispatcher->dispatch(
            TaskAssigned::create(
                $task->getId(),
                $task->getTitle(),
                $command->userId,
                $command->assignedBy,
                $task->getProjectId()
            )
        );

        return $task;
    }
}
