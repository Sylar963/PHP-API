<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\CreateTaskCommand;
use App\Domain\Entities\Task;
use App\Domain\Events\TaskCreated;
use App\Domain\Repositories\TaskRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Contracts\Events\Dispatcher;

class CreateTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private Dispatcher $eventDispatcher
    ) {
    }

    public function handle(CreateTaskCommand $command): Task
    {
        $task = new Task(
            id: Str::uuid()->toString(),
            title: $command->title,
            description: $command->description,
            status: $command->status,
            priority: $command->priority,
            projectId: $command->projectId,
            assignedTo: $command->assignedTo,
            dueDate: $command->dueDate
        );

        $this->taskRepository->save($task);

        // Dispatch domain event
        $this->eventDispatcher->dispatch(
            TaskCreated::create(
                $task->getId(),
                $task->getTitle(),
                $task->getProjectId(),
                $task->getAssignedTo()
            )
        );

        return $task;
    }
}
