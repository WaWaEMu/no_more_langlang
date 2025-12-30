<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\PetCreatorInterface;
use App\Services\PetCreationService;
use App\Contracts\AdoptionApplicationInterface;
use App\Services\AdoptionApplicationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PetCreatorInterface::class, PetCreationService::class);
        $this->app->bind(AdoptionApplicationInterface::class, AdoptionApplicationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
