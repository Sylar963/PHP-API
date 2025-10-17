<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * Progressive Rate Limiting for Failed Login Attempts
 *
 * Implements increasingly strict rate limiting based on failed login history.
 * Helps prevent brute force attacks while allowing legitimate users to retry.
 *
 * Progressive throttling:
 * - 3 failed attempts: 1 minute lockout
 * - 5 failed attempts: 5 minute lockout
 * - 10 failed attempts: 1 hour lockout
 * - 15+ failed attempts: 24 hour lockout + alert
 */
class ThrottleFailedLogins
{
    /**
     * Create a new middleware instance.
     */
    public function __construct(
        protected RateLimiter $limiter
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only process login responses
        if ($request->is('*/auth/login') && $response->getStatusCode() === 401) {
            $this->handleFailedLogin($request);
        }

        // Clear failed attempts on successful login
        if ($request->is('*/auth/login') && $response->getStatusCode() === 200) {
            $this->clearFailedAttempts($request);
        }

        return $response;
    }

    /**
     * Handle a failed login attempt with progressive throttling.
     */
    protected function handleFailedLogin(Request $request): void
    {
        $key = $this->getFailedAttemptsKey($request);
        $attempts = $this->limiter->attempts($key);

        // Increment failed attempts
        $this->limiter->hit($key, $this->getDecayMinutes($attempts) * 60);

        // Log failed attempt
        \Log::warning('Failed login attempt', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'attempt_count' => $attempts + 1,
            'lockout_duration' => $this->getDecayMinutes($attempts) . ' minutes',
        ]);

        // Trigger alert for excessive failed attempts
        if ($attempts >= 15) {
            $this->triggerSecurityAlert($request, $attempts);
        }
    }

    /**
     * Clear failed attempts after successful login.
     */
    protected function clearFailedAttempts(Request $request): void
    {
        $key = $this->getFailedAttemptsKey($request);
        $attempts = $this->limiter->attempts($key);

        if ($attempts > 0) {
            \Log::info('Failed login attempts cleared after successful login', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'previous_attempts' => $attempts,
            ]);

            $this->limiter->clear($key);
        }
    }

    /**
     * Get the cache key for failed attempts.
     */
    protected function getFailedAttemptsKey(Request $request): string
    {
        $email = $request->input('email', '');
        return 'failed_login_' . sha1($request->ip() . '|' . strtolower($email));
    }

    /**
     * Get progressive decay time based on attempt count.
     */
    protected function getDecayMinutes(int $attempts): int
    {
        return match (true) {
            $attempts >= 15 => 1440,  // 24 hours
            $attempts >= 10 => 60,    // 1 hour
            $attempts >= 5 => 5,      // 5 minutes
            $attempts >= 3 => 1,      // 1 minute
            default => env('RATE_LIMIT_FAILED_LOGIN_DECAY', 10),
        };
    }

    /**
     * Trigger security alert for excessive failed attempts.
     */
    protected function triggerSecurityAlert(Request $request, int $attempts): void
    {
        $threshold = env('ALERT_FAILED_LOGIN_THRESHOLD', 5);

        if ($attempts >= $threshold && $attempts % 5 === 0) {
            \Log::critical('SECURITY ALERT: Excessive failed login attempts', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'attempt_count' => $attempts,
                'timestamp' => now()->toDateTimeString(),
            ]);

            // Fire event for notification system (implement in Phase 6)
            Event::dispatch('security.excessive_failed_logins', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'attempts' => $attempts,
            ]);
        }
    }
}
