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

// Security status endpoint (admin-only)
Route::get('/security/status', function () {
    // This endpoint should only be accessible in local environment or by Super Admins
    if (!app()->environment('local') && (!auth('sanctum')->check() || !auth('sanctum')->user()->hasRole('Super Admin'))) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    return response()->json([
        'security_features' => [
            'rate_limiting' => [
                'auth_endpoints' => env('RATE_LIMIT_AUTH', 5) . ' requests/minute',
                'api_endpoints' => env('RATE_LIMIT_API', 60) . ' requests/minute',
                'progressive_throttling' => 'enabled',
            ],
            'authentication' => [
                'sanctum_token_expiration' => env('SANCTUM_EXPIRATION', 480) . ' minutes',
                'token_prefix' => env('SANCTUM_TOKEN_PREFIX', 'pmapp_'),
                'session_encryption' => env('SESSION_ENCRYPT', false) ? 'enabled' : 'disabled',
            ],
            'security_headers' => [
                'enabled' => env('SECURITY_HEADERS_ENABLED', false) ? 'yes' : 'no',
                'hsts' => 'configured',
                'csp' => env('CSP_ENABLED', false) ? 'enabled' : 'disabled',
                'x_frame_options' => 'DENY',
            ],
            'cors' => [
                'allowed_origins' => env('CORS_ALLOWED_ORIGINS', 'localhost'),
                'credentials_support' => env('CORS_SUPPORTS_CREDENTIALS', true) ? 'yes' : 'no',
            ],
            'infrastructure' => [
                'queue_encryption' => env('QUEUE_ENCRYPTION', false) ? 'enabled' : 'disabled',
                'database_ssl' => env('DB_SSL_CA') ? 'configured' : 'not configured',
                'redis_password' => env('REDIS_PASSWORD') ? 'set' : 'not set',
            ],
            'rbac' => [
                'roles_configured' => \Spatie\Permission\Models\Role::count() . ' roles',
                'permissions_configured' => \Spatie\Permission\Models\Permission::count() . ' permissions',
            ],
        ],
        'environment' => app()->environment(),
        'debug_mode' => config('app.debug') ? 'ENABLED (DISABLE IN PRODUCTION!)' : 'disabled',
        'timestamp' => now()->toDateTimeString(),
    ]);
})->middleware(['auth:sanctum']);

// Authentication routes (public) - with rate limiting
Route::prefix('auth')->middleware([
    \App\Http\Middleware\RateLimitAuth::class,
    \App\Http\Middleware\ThrottleFailedLogins::class,
])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes (authentication required) - with rate limiting
Route::middleware([
    'auth:sanctum',
    \App\Http\Middleware\RateLimitApi::class,
])->group(function () {
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
