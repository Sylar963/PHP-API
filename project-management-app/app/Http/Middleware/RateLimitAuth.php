<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rate Limiting Middleware for Authentication Routes
 *
 * Implements strict rate limiting for authentication endpoints to prevent
 * brute force attacks and credential stuffing.
 *
 * Default: 5 attempts per minute per IP address
 */
class RateLimitAuth
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
        $maxAttempts = config('app.rate_limit_auth', env('RATE_LIMIT_AUTH', 5));
        $decayMinutes = 1;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $seconds = $this->limiter->availableIn($key);

            // Log the rate limit breach for security monitoring
            \Log::warning('Authentication rate limit exceeded', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
                'user_agent' => $request->userAgent(),
                'retry_after' => $seconds,
            ]);

            return response()->json([
                'message' => 'Too many authentication attempts. Please try again later.',
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
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - $this->limiter->attempts($key)));

        return $response;
    }

    /**
     * Resolve the request signature for rate limiting.
     *
     * Uses IP address and email (if present) to create unique key
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $email = $request->input('email', '');
        return 'auth_' . sha1($request->ip() . '|' . $email);
    }
}
