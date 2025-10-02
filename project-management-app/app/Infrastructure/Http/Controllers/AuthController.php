<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\Commands\RegisterUserCommand;
use App\Application\Commands\LoginUserCommand;
use App\Application\Handlers\RegisterUserHandler;
use App\Application\Handlers\LoginUserHandler;
use App\Infrastructure\Http\Requests\RegisterRequest;
use App\Infrastructure\Http\Requests\LoginRequest;
use App\Application\DTOs\UserDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function __construct(
        private RegisterUserHandler $registerUserHandler,
        private LoginUserHandler $loginUserHandler
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $command = new RegisterUserCommand(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
            role: $request->input('role', 'team_member')
        );

        $user = $this->registerUserHandler->handle($command);
        $userDTO = UserDTO::fromEntity($user);

        return response()->json([
            'message' => 'User registered successfully',
            'data' => $userDTO->toArray()
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $command = new LoginUserCommand(
            email: $request->input('email'),
            password: $request->input('password'),
            deviceName: $request->input('device_name', 'api-client')
        );

        $result = $this->loginUserHandler->handle($command);
        $userDTO = UserDTO::fromEntity($result['user']);

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => $userDTO->toArray(),
                'token' => $result['token']
            ]
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_active' => $user->is_active,
            ]
        ]);
    }
}
