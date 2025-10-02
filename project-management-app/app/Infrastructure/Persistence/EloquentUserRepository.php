<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Infrastructure\Eloquent\UserModel;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(string $id): ?User
    {
        $model = UserModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $model = UserModel::where('email', $email)->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(): array
    {
        return UserModel::all()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function save(User $user): void
    {
        $data = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'is_active' => $user->isActive(),
        ];

        // Only include password if it's set (for new users or password updates)
        if ($user->getPassword()) {
            $data['password'] = $user->getPassword();
        }

        UserModel::updateOrCreate(
            ['id' => $user->getId() ?: null],
            $data
        );
    }

    public function delete(string $id): void
    {
        UserModel::destroy($id);
    }

    public function findByRole(string $role): array
    {
        return UserModel::where('role', $role)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findActiveUsers(): array
    {
        return UserModel::where('is_active', true)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function existsByEmail(string $email): bool
    {
        return UserModel::where('email', $email)->exists();
    }

    private function toDomain(UserModel $model): User
    {
        return new User(
            id: (string)$model->id,
            name: $model->name,
            email: $model->email,
            role: $model->role,
            isActive: $model->is_active
        );
    }
}
