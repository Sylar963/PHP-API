<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\Commands\CreateTeamCommand;
use App\Application\Services\TeamManagementService;
use App\Infrastructure\Http\Requests\CreateTeamRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TeamController extends Controller
{
    public function __construct(
        private TeamManagementService $teamManagementService
    ) {
    }

    public function index(): JsonResponse
    {
        $teams = $this->teamManagementService->getAllTeams();

        return response()->json([
            'data' => array_map(fn($dto) => $dto->toArray(), $teams)
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $team = $this->teamManagementService->getTeam($id);

        return response()->json(['data' => $team->toArray()]);
    }

    public function store(CreateTeamRequest $request): JsonResponse
    {
        $command = new CreateTeamCommand(
            name: $request->input('name'),
            description: $request->input('description')
        );

        $team = $this->teamManagementService->createTeam($command);

        return response()->json(['data' => $team->toArray()], 201);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->teamManagementService->deleteTeam($id);

        return response()->json(['message' => 'Team deleted successfully'], 204);
    }

    public function addMember(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $this->teamManagementService->addMember($id, $request->input('user_id'));

        return response()->json(['message' => 'Member added successfully']);
    }

    public function removeMember(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $this->teamManagementService->removeMember($id, $request->input('user_id'));

        return response()->json(['message' => 'Member removed successfully']);
    }

    public function members(string $id): JsonResponse
    {
        $members = $this->teamManagementService->getMembers($id);

        return response()->json(['data' => $members]);
    }
}
