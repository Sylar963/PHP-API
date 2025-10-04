<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\Commands\AssignTaskCommand;
use App\Application\Commands\CreateTaskCommand;
use App\Application\Commands\UpdateTaskCommand;
use App\Application\Commands\UpdateTaskStatusCommand;
use App\Application\Services\TaskManagementService;
use App\Infrastructure\Http\Requests\CreateTaskRequest;
use App\Infrastructure\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TaskController extends Controller
{
    public function __construct(
        private TaskManagementService $taskManagementService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $projectId = $request->query('project_id');

        if ($projectId) {
            $tasks = $this->taskManagementService->getProjectTasks($projectId);
        } else {
            $userId = auth()->id() ?? 'guest';
            $tasks = $this->taskManagementService->getUserTasks($userId);
        }

        return response()->json([
            'data' => array_map(fn($dto) => $dto->toArray(), $tasks)
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $task = $this->taskManagementService->getTask($id);

        return response()->json(['data' => $task->toArray()]);
    }

    public function store(CreateTaskRequest $request): JsonResponse
    {
        $command = new CreateTaskCommand(
            title: $request->input('title'),
            description: $request->input('description'),
            status: $request->input('status', 'todo'),
            priority: $request->input('priority', 'medium'),
            projectId: $request->input('project_id'),
            assignedTo: $request->input('assigned_to'),
            dueDate: $request->input('due_date') ? new \DateTimeImmutable($request->input('due_date')) : null
        );

        $task = $this->taskManagementService->createTask($command);

        return response()->json(['data' => $task->toArray()], 201);
    }

    public function update(UpdateTaskRequest $request, string $id): JsonResponse
    {
        $userId = auth()->id() ?? 'system';

        $command = new UpdateTaskCommand(
            taskId: $id,
            title: $request->input('title'),
            description: $request->input('description'),
            status: $request->input('status'),
            priority: $request->input('priority'),
            dueDate: $request->input('due_date') ? new \DateTimeImmutable($request->input('due_date')) : null
        );

        $task = $this->taskManagementService->updateTask($command, $userId);

        return response()->json(['data' => $task->toArray()]);
    }

    public function assign(Request $request, string $id): JsonResponse
    {
        $userId = auth()->id() ?? 'system';

        $command = new AssignTaskCommand(
            taskId: $id,
            userId: $request->input('user_id'),
            assignedBy: $userId
        );

        $task = $this->taskManagementService->assignTask($command, $userId);

        return response()->json(['data' => $task->toArray()]);
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $userId = auth()->id() ?? 'system';

        $command = new UpdateTaskStatusCommand(
            taskId: $id,
            newStatus: $request->input('status'),
            updatedBy: $userId
        );

        $task = $this->taskManagementService->updateTaskStatus($command, $userId);

        return response()->json(['data' => $task->toArray()]);
    }

    public function destroy(string $id): JsonResponse
    {
        $userId = auth()->id() ?? 'guest';
        $this->taskManagementService->deleteTask($id, $userId);

        return response()->json(['message' => 'Task deleted successfully'], 204);
    }
}
