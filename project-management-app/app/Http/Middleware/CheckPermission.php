<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permission Check Middleware
 *
 * Verifies that the authenticated user has the required permission(s)
 * to access a route. Works with Spatie Laravel Permission package.
 *
 * Usage in routes:
 * Route::get('/projects', [ProjectController::class, 'index'])
 *     ->middleware('permission:view projects');
 */
class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  string|array  $permission
     */
    public function handle(Request $request, Closure $next, string $permission, ?string $guard = null): Response
    {
        $authGuard = app('auth')->guard($guard);

        if ($authGuard->guest()) {
            return response()->json([
                'message' => 'Unauthenticated. Please log in to access this resource.',
            ], 401);
        }

        $user = $authGuard->user();

        // Support multiple permissions separated by pipe (|) or comma (,)
        $permissions = $this->parsePermissions($permission);

        // Check if user has any of the required permissions
        if (!$user->hasAnyPermission($permissions)) {
            \Log::warning('Permission denied', [
                'user_id' => $user->id,
                'email' => $user->email,
                'required_permissions' => $permissions,
                'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                'route' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Forbidden. You do not have permission to access this resource.',
                'required_permission' => $permissions,
            ], 403);
        }

        return $next($request);
    }

    /**
     * Parse permissions string into array.
     *
     * Supports: "view projects", "view projects|edit projects", "view projects,edit projects"
     */
    protected function parsePermissions(string $permission): array
    {
        // Handle both pipe and comma separators
        if (str_contains($permission, '|')) {
            return array_map('trim', explode('|', $permission));
        }

        if (str_contains($permission, ',')) {
            return array_map('trim', explode(',', $permission));
        }

        return [$permission];
    }
}
