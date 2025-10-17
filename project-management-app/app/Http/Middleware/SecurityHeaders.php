<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Headers Middleware
 *
 * Implements comprehensive security headers to protect against common web vulnerabilities:
 * - Content Security Policy (CSP)
 * - HTTP Strict Transport Security (HSTS)
 * - X-Frame-Options (Clickjacking protection)
 * - X-Content-Type-Options (MIME sniffing protection)
 * - Referrer-Policy
 * - Permissions-Policy
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply security headers if enabled
        if (!config('app.security_headers_enabled', env('SECURITY_HEADERS_ENABLED', false))) {
            return $response;
        }

        // HTTP Strict Transport Security (HSTS)
        // Forces browsers to use HTTPS for all future requests
        if ($this->shouldSetHsts()) {
            $maxAge = config('app.hsts_max_age', env('HSTS_MAX_AGE', 31536000));
            $response->headers->set('Strict-Transport-Security', "max-age={$maxAge}; includeSubDomains; preload");
        }

        // Content Security Policy (CSP)
        // Prevents XSS attacks by controlling allowed content sources
        if (config('app.csp_enabled', env('CSP_ENABLED', false))) {
            $response->headers->set('Content-Security-Policy', $this->getContentSecurityPolicy());
        }

        // X-Frame-Options
        // Prevents clickjacking attacks by disallowing iframe embedding
        $response->headers->set('X-Frame-Options', 'DENY');

        // X-Content-Type-Options
        // Prevents MIME type sniffing attacks
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-XSS-Protection (legacy, but still useful for older browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy
        // Controls how much referrer information is shared
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy (formerly Feature-Policy)
        // Restricts access to browser features
        $response->headers->set('Permissions-Policy', $this->getPermissionsPolicy());

        // Remove server information disclosure headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }

    /**
     * Determine if HSTS header should be set.
     * Only set HSTS on HTTPS connections.
     */
    protected function shouldSetHsts(): bool
    {
        return request()->secure() || app()->environment('production');
    }

    /**
     * Get Content Security Policy directive.
     *
     * Customize this based on your application's needs.
     */
    protected function getContentSecurityPolicy(): string
    {
        $appUrl = config('app.url');

        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: https:",
            "font-src 'self' data:",
            "connect-src 'self' {$appUrl}",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "upgrade-insecure-requests",
        ]);
    }

    /**
     * Get Permissions Policy directive.
     *
     * Restricts access to browser features and APIs.
     */
    protected function getPermissionsPolicy(): string
    {
        return implode(', ', [
            'accelerometer=()',
            'camera=()',
            'geolocation=()',
            'gyroscope=()',
            'magnetometer=()',
            'microphone=()',
            'payment=()',
            'usb=()',
        ]);
    }
}
