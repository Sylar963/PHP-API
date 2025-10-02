<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\Commands\CreateProjectCommand;
use App\Application\Commands\UpdateProjectCommand;
use App\Application\Services\ProjectManagementService;
use App\Infrastructure\Http\Requests\CreateProjectRequest;
use App\Infrastructure\Http\Requests\UpdateProjectRequest;
use App\Infrastructure\Http\Resources\ProjectResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectManagementService $projectManagementService
    ) {
    }

    public function index(): JsonResponse
    {
        $projects = $this->projectManagementService->getAllProjects();

        return response()->json([
            'data' => array_map(fn($dto) => $dto->toArray(), $projects)
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $userId = auth()->id() ?? 'guest';
        $project = $this->projectManagementService->getProject($id, $userId);

        return response()->json(['data' => $project->toArray()]);
    }

    public function store(CreateProjectRequest $request): JsonResponse
    {
        $command = new CreateProjectCommand(
            name: $request->input('name'),
            description: $request->input('description'),
            status: $request->input('status'),
            ownerId: (string)(auth()->id() ?? 1), // Default to user ID 1 for testing
            startDate: $request->input('start_date') ? new \DateTimeImmutable($request->input('start_date')) : null,
            endDate: $request->input('end_date') ? new \DateTimeImmutable($request->input('end_date')) : null
        );

        $project = $this->projectManagementService->createProject($command);

        return response()->json(['data' => $project->toArray()], 201);
    }

    public function update(UpdateProjectRequest $request, string $id): JsonResponse
    {
        $userId = auth()->id() ?? 'guest';

        $command = new UpdateProjectCommand(
            projectId: $id,
            name: $request->input('name'),
            description: $request->input('description'),
            status: $request->input('status'),
            startDate: $request->input('start_date') ? new \DateTimeImmutable($request->input('start_date')) : null,
            endDate: $request->input('end_date') ? new \DateTimeImmutable($request->input('end_date')) : null
        );

        $project = $this->projectManagementService->updateProject($command, $userId);

        return response()->json(['data' => $project->toArray()]);
    }

    public function destroy(string $id): JsonResponse
    {
        $userId = auth()->id() ?? 'guest';
        $this->projectManagementService->deleteProject($id, $userId);

        return response()->json(['message' => 'Project deleted successfully'], 204);
    }
}
