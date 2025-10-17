<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rate Limiting Middleware for API Routes
 *
 * Implements rate limiting for general API endpoints to prevent abuse
 * and ensure fair resource usage.
 *
 * Default: 60 requests per minute per authenticated user
 */
class RateLimitApi
{
    /**
     * Create a new middleware instance.
     */
    public function __construct(
        protected RateLimiter $limiter
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = config('app.rate_limit_api', env('RATE_LIMIT_API', 60));
        $decayMinutes = 1;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $seconds = $this->limiter->availableIn($key);

            // Log excessive API usage
            \Log::info('API rate limit exceeded', [
                'user_id' => $request->user()?->id,
                'ip' => $request->ip(),
                'route' => $request->path(),
                'method' => $request->method(),
                'retry_after' => $seconds,
            ]);

            return response()->json([
                'message' => 'Too many requests. Please slow down.',
                'retry_after' => $seconds,
            ], 429, [
                'Retry-After' => $seconds,
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers to response
        $attempts = $this->limiter->attempts($key);
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - $attempts));

        if ($attempts >= $maxAttempts) {
            $response->headers->set('X-RateLimit-Reset', now()->addSeconds($this->limiter->availableIn($key))->timestamp);
        }

        return $response;
    }

    /**
     * Resolve the request signature for rate limiting.
     *
     * Uses user ID for authenticated requests, IP for unauthenticated
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $user = $request->user();

        if ($user) {
            return 'api_user_' . $user->id;
        }

        return 'api_ip_' . sha1($request->ip());
    }
}
