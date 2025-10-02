<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Repositories\ProjectRepositoryInterface;
use App\Domain\Repositories\TaskRepositoryInterface;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Repositories\TeamRepositoryInterface;
use App\Domain\Repositories\MilestoneRepositoryInterface;
use App\Domain\Repositories\TimeEntryRepositoryInterface;
use App\Infrastructure\Persistence\EloquentProjectRepository;
use App\Infrastructure\Persistence\EloquentTaskRepository;
use App\Infrastructure\Persistence\EloquentUserRepository;
use App\Infrastructure\Persistence\EloquentTeamRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Domain Repository Interfaces to Infrastructure Implementations
        $this->app->bind(ProjectRepositoryInterface::class, EloquentProjectRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, EloquentTaskRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(TeamRepositoryInterface::class, EloquentTeamRepository::class);

        // Add more bindings as you create the implementations
        // $this->app->bind(MilestoneRepositoryInterface::class, EloquentMilestoneRepository::class);
        // $this->app->bind(TimeEntryRepositoryInterface::class, EloquentTimeEntryRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
