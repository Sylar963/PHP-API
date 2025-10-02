<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Team;
use App\Domain\Repositories\TeamRepositoryInterface;
use App\Infrastructure\Eloquent\TeamModel;

class EloquentTeamRepository implements TeamRepositoryInterface
{
    public function findById(string $id): ?Team
    {
        $model = TeamModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(): array
    {
        return TeamModel::all()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function save(Team $team): void
    {
        TeamModel::updateOrCreate(
            ['id' => $team->getId() ?: null],
            [
                'name' => $team->getName(),
                'description' => $team->getDescription(),
            ]
        );
    }

    public function delete(string $id): void
    {
        TeamModel::destroy($id);
    }

    public function findByName(string $name): ?Team
    {
        $model = TeamModel::where('name', $name)->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function addMember(string $teamId, int $userId): void
    {
        $team = TeamModel::find($teamId);
        if ($team) {
            $team->members()->attach($userId);
        }
    }

    public function removeMember(string $teamId, int $userId): void
    {
        $team = TeamModel::find($teamId);
        if ($team) {
            $team->members()->detach($userId);
        }
    }

    public function getMembers(string $teamId): array
    {
        $team = TeamModel::find($teamId);
        if (!$team) {
            return [];
        }

        return $team->members->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ];
        })->toArray();
    }

    public function findByMemberId(int $userId): array
    {
        return TeamModel::whereHas('members', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function existsById(string $id): bool
    {
        return TeamModel::where('id', $id)->exists();
    }

    private function toDomain(TeamModel $model): Team
    {
        return new Team(
            id: $model->id,
            name: $model->name,
            description: $model->description
        );
    }
}
