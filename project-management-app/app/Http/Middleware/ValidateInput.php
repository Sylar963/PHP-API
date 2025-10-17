<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Input Validation Middleware
 *
 * Provides global input sanitization to prevent common attacks:
 * - XSS (Cross-Site Scripting)
 * - SQL Injection (via parameter type checking)
 * - Null byte injection
 * - Directory traversal attempts
 */
class ValidateInput
{
    /**
     * Suspicious patterns that might indicate attacks
     */
    protected array $suspiciousPatterns = [
        // SQL injection patterns
        '/(\bunion\b.*\bselect\b)/i',
        '/(\bor\b.*=.*)/i',
        '/(\'|(\\x27))|(--)|(%27)/i',

        // XSS patterns
        '/<script[^>]*>.*?<\/script>/is',
        '/javascript:/i',
        '/on\w+\s*=/i',

        // Path traversal
        '/\.\.\//',
        '/\.\.\\\\/',

        // Null bytes
        '/\x00/',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for suspicious patterns in request data
        if ($this->containsSuspiciousContent($request)) {
            \Log::warning('Suspicious input detected', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'input' => $request->except(['password', 'password_confirmation']),
            ]);

            return response()->json([
                'message' => 'Invalid input detected. Please check your request and try again.',
            ], 400);
        }

        // Sanitize input data (optional - be careful with this)
        // $request->merge($this->sanitizeInput($request->all()));

        return $next($request);
    }

    /**
     * Check if request contains suspicious content.
     */
    protected function containsSuspiciousContent(Request $request): bool
    {
        $data = $request->except(['password', 'password_confirmation']);

        return $this->scanArray($data);
    }

    /**
     * Recursively scan array for suspicious patterns.
     */
    protected function scanArray(array $data): bool
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($this->scanArray($value)) {
                    return true;
                }
            } elseif (is_string($value)) {
                if ($this->matchesSuspiciousPattern($value) || $this->matchesSuspiciousPattern($key)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if string matches any suspicious pattern.
     */
    protected function matchesSuspiciousPattern(string $input): bool
    {
        foreach ($this->suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitize input data (use with caution).
     *
     * Note: This is disabled by default as it can interfere with legitimate data.
     * Consider using Laravel's validation rules instead.
     */
    protected function sanitizeInput(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeInput($value);
            } elseif (is_string($value)) {
                // Remove null bytes
                $value = str_replace("\0", '', $value);

                // Trim whitespace
                $value = trim($value);

                $data[$key] = $value;
            }
        }

        return $data;
    }
}
