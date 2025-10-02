<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\CreateUserCommand;
use App\Application\Exceptions\UserAlreadyExistsException;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CreateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(CreateUserCommand $command): User
    {
        // Check if user already exists
        if ($this->userRepository->existsByEmail($command->email)) {
            throw new UserAlreadyExistsException("User with email {$command->email} already exists");
        }

        $user = new User(
            id: Str::uuid()->toString(),
            name: $command->name,
            email: $command->email,
            role: $command->role,
            isActive: true
        );

        $this->userRepository->save($user);

        return $user;
    }
}
