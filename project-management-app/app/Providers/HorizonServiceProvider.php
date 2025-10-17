<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

/**
 * Horizon Service Provider
 *
 * Secures Laravel Horizon dashboard with role-based access control.
 * Only Super Admins and authorized users can access queue monitoring.
 */
class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     *
     * SECURITY: Only Super Admins can access Horizon dashboard
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user) {
            // In local environment, allow all authenticated users
            if (app()->environment('local')) {
                return true;
            }

            // In production, only allow Super Admins
            // Check if user has the Super Admin role
            if (method_exists($user, 'hasRole')) {
                return $user->hasRole('Super Admin');
            }

            // Fallback: check email whitelist for emergency access
            $allowedEmails = explode(',', env('HORIZON_ALLOWED_EMAILS', ''));
            return in_array($user->email, $allowedEmails);
        });
    }
}
