<?php

use App\Infrastructure\Http\Controllers\AuthController;
use App\Infrastructure\Http\Controllers\ProjectController;
use App\Infrastructure\Http\Controllers\TaskController;
use App\Infrastructure\Http\Controllers\TeamController;
use App\Infrastructure\Http\Controllers\UserController;
use App\Infrastructure\Http\Controllers\MilestoneController;
use App\Infrastructure\Http\Controllers\TimeEntryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'OK', 'message' => 'API is running']);
});

// Authentication routes (public)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Public routes for testing (remove in production)
Route::prefix('public')->group(function () {
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/teams', [TeamController::class, 'index']);
    Route::post('/teams', [TeamController::class, 'store']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/milestones', [MilestoneController::class, 'index']);
    Route::post('/milestones', [MilestoneController::class, 'store']);
    Route::get('/time-entries', [TimeEntryController::class, 'index']);
    Route::post('/time-entries', [TimeEntryController::class, 'store']);
});

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // Project routes
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::post('/', [ProjectController::class, 'store']);
        Route::get('/{id}', [ProjectController::class, 'show']);
        Route::put('/{id}', [ProjectController::class, 'update']);
        Route::delete('/{id}', [ProjectController::class, 'destroy']);
    });

    // Task routes
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::post('/', [TaskController::class, 'store']);
        Route::get('/{id}', [TaskController::class, 'show']);
        Route::put('/{id}', [TaskController::class, 'update']);
        Route::put('/{id}/assign', [TaskController::class, 'assign']);
        Route::put('/{id}/status', [TaskController::class, 'updateStatus']);
        Route::delete('/{id}', [TaskController::class, 'destroy']);
    });

    // Team routes
    Route::prefix('teams')->group(function () {
        Route::get('/', [TeamController::class, 'index']);
        Route::post('/', [TeamController::class, 'store']);
        Route::get('/{id}', [TeamController::class, 'show']);
        Route::put('/{id}', [TeamController::class, 'update']);
        Route::delete('/{id}', [TeamController::class, 'destroy']);
        Route::post('/{id}/members', [TeamController::class, 'addMember']);
        Route::delete('/{id}/members', [TeamController::class, 'removeMember']);
        Route::get('/{id}/members', [TeamController::class, 'members']);
    });

    // User routes
    Route::get('/users', [UserController::class, 'index']);

    // Milestone routes
    Route::prefix('milestones')->group(function () {
        Route::get('/', [MilestoneController::class, 'index']);
        Route::post('/', [MilestoneController::class, 'store']);
        Route::get('/{id}', [MilestoneController::class, 'show']);
        Route::put('/{id}', [MilestoneController::class, 'update']);
        Route::delete('/{id}', [MilestoneController::class, 'destroy']);
    });

    // Time Entry routes
    Route::prefix('time-entries')->group(function () {
        Route::get('/', [TimeEntryController::class, 'index']);
        Route::post('/', [TimeEntryController::class, 'store']);
        Route::get('/{id}', [TimeEntryController::class, 'show']);
        Route::put('/{id}', [TimeEntryController::class, 'update']);
        Route::delete('/{id}', [TimeEntryController::class, 'destroy']);
    });
});
