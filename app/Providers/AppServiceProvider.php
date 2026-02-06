<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\PetCreatorInterface;
use App\Services\PetCreationService;
use App\Contracts\AdoptionApplicationInterface;
use App\Services\AdoptionApplicationService;
use App\Contracts\PetServiceInterface;
use App\Services\PetService;
use App\Contracts\PetCommentServiceInterface;
use App\Services\PetCommentService;
use App\Contracts\UserServiceInterface;
use App\Services\UserService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PetCreatorInterface::class, PetCreationService::class);
        $this->app->bind(AdoptionApplicationInterface::class, AdoptionApplicationService::class);
        $this->app->bind(
            \App\Contracts\PetServiceInterface::class,
            \App\Services\PetService::class
        );

        $this->app->bind(
            \App\Contracts\PetCommentServiceInterface::class,
            \App\Services\PetCommentService::class
        );

        $this->app->bind(
            \App\Contracts\UserServiceInterface::class,
            \App\Services\UserService::class
        );

        $this->app->bind(
            \App\Services\AdoptionCaseServiceInterface::class,
            \App\Services\AdoptionCaseService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
