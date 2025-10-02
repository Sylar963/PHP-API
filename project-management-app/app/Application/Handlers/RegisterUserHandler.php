<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\RegisterUserCommand;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Application\Exceptions\UserAlreadyExistsException;
use App\Infrastructure\Eloquent\UserModel;
use Illuminate\Support\Facades\Hash;

class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(RegisterUserCommand $command): User
    {
        // Check if user already exists
        $existingUser = $this->userRepository->findByEmail($command->email);
        if ($existingUser) {
            throw new UserAlreadyExistsException("User with email {$command->email} already exists");
        }

        // Create user in database
        $userModel = UserModel::create([
            'name' => $command->name,
            'email' => $command->email,
            'password' => Hash::make($command->password),
            'role' => $command->role,
            'is_active' => true,
        ]);

        // Convert to domain entity and return
        return new User(
            id: (string)$userModel->id,
            name: $userModel->name,
            email: $userModel->email,
            role: $userModel->role,
            isActive: $userModel->is_active
        );
    }
}
