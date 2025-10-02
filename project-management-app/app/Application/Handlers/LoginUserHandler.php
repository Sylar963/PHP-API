<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\LoginUserCommand;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Application\Exceptions\UnauthorizedException;
use App\Infrastructure\Eloquent\UserModel;
use Illuminate\Support\Facades\Hash;

class LoginUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(LoginUserCommand $command): array
    {
        // Find user model (we need password for verification)
        $userModel = UserModel::where('email', $command->email)->first();

        if (!$userModel) {
            throw new UnauthorizedException('Invalid credentials');
        }

        // Verify password
        if (!Hash::check($command->password, $userModel->password)) {
            throw new UnauthorizedException('Invalid credentials');
        }

        // Check if user is active
        if (!$userModel->is_active) {
            throw new UnauthorizedException('Account is inactive');
        }

        // Generate API token using Sanctum
        $token = $userModel->createToken(
            $command->deviceName ?? 'api-token'
        )->plainTextToken;

        // Get domain user entity
        $user = $this->userRepository->findByEmail($command->email);

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
