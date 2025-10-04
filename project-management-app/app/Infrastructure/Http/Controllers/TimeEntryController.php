<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\Commands\CreateTimeEntryCommand;
use App\Application\Commands\UpdateTimeEntryCommand;
use App\Application\Services\TimeEntryManagementService;
use App\Infrastructure\Http\Requests\CreateTimeEntryRequest;
use App\Infrastructure\Http\Requests\UpdateTimeEntryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TimeEntryController extends Controller
{
    public function __construct(
        private TimeEntryManagementService $timeEntryManagementService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $taskId = $request->query('task_id');
        $timeEntries = $this->timeEntryManagementService->getAllTimeEntries($taskId);

        return response()->json([
            'data' => array_map(fn($dto) => $dto->toArray(), $timeEntries)
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $timeEntry = $this->timeEntryManagementService->getTimeEntry($id);

        return response()->json(['data' => $timeEntry->toArray()]);
    }

    public function store(CreateTimeEntryRequest $request): JsonResponse
    {
        $command = new CreateTimeEntryCommand(
            userId: (string) auth()->id(),
            taskId: $request->input('task_id'),
            startTime: $request->input('start_time'),
            description: $request->input('description', ''),
            endTime: $request->input('end_time')
        );

        $timeEntry = $this->timeEntryManagementService->createTimeEntry($command);

        return response()->json(['data' => $timeEntry->toArray()], 201);
    }

    public function update(UpdateTimeEntryRequest $request, string $id): JsonResponse
    {
        $command = new UpdateTimeEntryCommand(
            id: $id,
            description: $request->input('description'),
            endTime: $request->input('end_time')
        );

        $timeEntry = $this->timeEntryManagementService->updateTimeEntry($command);

        return response()->json(['data' => $timeEntry->toArray()]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->timeEntryManagementService->deleteTimeEntry($id);

        return response()->json(['message' => 'Time entry deleted successfully'], 204);
    }
}
