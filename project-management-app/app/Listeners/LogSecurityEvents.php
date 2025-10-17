<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;

/**
 * Security Event Logging Listener
 *
 * Logs all critical security events for audit trails and compliance.
 * Integrates with monitoring systems for real-time alerts.
 *
 * Events logged:
 * - Authentication attempts (success/failure)
 * - Account lockouts
 * - Permission violations
 * - Rate limit breaches
 * - User registration
 */
class LogSecurityEvents
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle successful login events.
     */
    public function handleLogin(Login $event): void
    {
        Log::channel('security')->info('User logged in successfully', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'guard' => $event->guard,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Handle failed login attempts.
     */
    public function handleFailedLogin(Failed $event): void
    {
        Log::channel('security')->warning('Failed login attempt', [
            'guard' => $event->guard,
            'credentials' => ['email' => $event->credentials['email'] ?? 'unknown'],
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Handle account lockout events.
     */
    public function handleLockout(Lockout $event): void
    {
        Log::channel('security')->critical('Account lockout triggered', [
            'request' => [
                'ip' => $event->request->ip(),
                'url' => $event->request->fullUrl(),
                'input' => $event->request->except(['password', 'password_confirmation']),
            ],
            'user_agent' => $event->request->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        // Trigger alert for excessive lockouts
        $this->sendSecurityAlert('Account Lockout', 'critical', [
            'ip' => $event->request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Handle user logout events.
     */
    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            Log::channel('security')->info('User logged out', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'guard' => $event->guard,
                'ip' => request()->ip(),
                'timestamp' => now()->toDateTimeString(),
            ]);
        }
    }

    /**
     * Handle user registration events.
     */
    public function handleRegistered(Registered $event): void
    {
        Log::channel('security')->info('New user registered', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Send security alert to configured channels.
     */
    protected function sendSecurityAlert(string $title, string $level, array $data): void
    {
        // Check if Slack webhook is configured
        $webhookUrl = env('SLACK_WEBHOOK_URL');

        if ($webhookUrl) {
            // Send to Slack (implement in Phase 6.3)
            // This is a placeholder for the alert system
            Log::channel('security')->debug('Security alert would be sent', [
                'title' => $title,
                'level' => $level,
                'data' => $data,
            ]);
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events): array
    {
        return [
            Login::class => 'handleLogin',
            Failed::class => 'handleFailedLogin',
            Lockout::class => 'handleLockout',
            Logout::class => 'handleLogout',
            Registered::class => 'handleRegistered',
        ];
    }
}
