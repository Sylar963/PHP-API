<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\Commands\CreateMilestoneCommand;
use App\Application\Commands\UpdateMilestoneCommand;
use App\Application\Services\MilestoneManagementService;
use App\Infrastructure\Http\Requests\CreateMilestoneRequest;
use App\Infrastructure\Http\Requests\UpdateMilestoneRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MilestoneController extends Controller
{
    public function __construct(
        private MilestoneManagementService $milestoneManagementService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $projectId = $request->query('project_id');
        $milestones = $this->milestoneManagementService->getAllMilestones($projectId);

        return response()->json([
            'data' => array_map(fn($dto) => $dto->toArray(), $milestones)
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $milestone = $this->milestoneManagementService->getMilestone($id);

        return response()->json(['data' => $milestone->toArray()]);
    }

    public function store(CreateMilestoneRequest $request): JsonResponse
    {
        $command = new CreateMilestoneCommand(
            projectId: $request->input('project_id'),
            name: $request->input('name'),
            description: $request->input('description'),
            dueDate: $request->input('due_date'),
            isCompleted: $request->input('is_completed', false)
        );

        $milestone = $this->milestoneManagementService->createMilestone($command);

        return response()->json(['data' => $milestone->toArray()], 201);
    }

    public function update(UpdateMilestoneRequest $request, string $id): JsonResponse
    {
        $command = new UpdateMilestoneCommand(
            id: $id,
            name: $request->input('name'),
            description: $request->input('description'),
            dueDate: $request->input('due_date'),
            isCompleted: $request->input('is_completed')
        );

        $milestone = $this->milestoneManagementService->updateMilestone($command);

        return response()->json(['data' => $milestone->toArray()]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->milestoneManagementService->deleteMilestone($id);

        return response()->json(['message' => 'Milestone deleted successfully'], 204);
    }
}
