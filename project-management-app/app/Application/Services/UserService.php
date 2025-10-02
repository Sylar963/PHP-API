<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Commands\CreateUserCommand;
use App\Application\DTOs\UserDTO;
use App\Application\Handlers\CreateUserHandler;
use App\Domain\Repositories\UserRepositoryInterface;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CreateUserHandler $createUserHandler
    ) {
    }

    public function createUser(CreateUserCommand $command): UserDTO
    {
        $user = $this->createUserHandler->handle($command);
        return UserDTO::fromEntity($user);
    }

    public function getUser(string $userId): UserDTO
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new \App\Application\Exceptions\TaskNotFoundException("User not found");
        }

        return UserDTO::fromEntity($user);
    }

    public function getUserByEmail(string $email): ?UserDTO
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return null;
        }

        return UserDTO::fromEntity($user);
    }

    public function getAllUsers(): array
    {
        $users = $this->userRepository->findAll();
        return array_map(
            fn($user) => UserDTO::fromEntity($user),
            $users
        );
    }

    public function getActiveUsers(): array
    {
        $users = $this->userRepository->findActiveUsers();
        return array_map(
            fn($user) => UserDTO::fromEntity($user),
            $users
        );
    }

    public function getUsersByRole(string $role): array
    {
        $users = $this->userRepository->findByRole($role);
        return array_map(
            fn($user) => UserDTO::fromEntity($user),
            $users
        );
    }
}
