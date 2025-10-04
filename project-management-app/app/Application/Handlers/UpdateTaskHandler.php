<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\UpdateTaskCommand;
use App\Application\Exceptions\TaskNotFoundException;
use App\Domain\Entities\Task;
use App\Domain\Repositories\TaskRepositoryInterface;

class UpdateTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    public function handle(UpdateTaskCommand $command): Task
    {
        $task = $this->taskRepository->findById($command->taskId);

        if (!$task) {
            throw new TaskNotFoundException("Task with ID {$command->taskId} not found");
        }

        if ($command->title !== null) {
            $task->updateTitle($command->title);
        }

        if ($command->description !== null) {
            $task->updateDescription($command->description);
        }

        if ($command->status !== null) {
            $task->updateStatus($command->status);
        }

        if ($command->priority !== null) {
            $task->updatePriority($command->priority);
        }

        if ($command->dueDate !== null) {
            $task->setDueDate($command->dueDate);
        }

        $this->taskRepository->save($task);

        return $task;
    }
}
