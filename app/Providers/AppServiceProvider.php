<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\PetCreatorInterface;
use App\Services\PetCreationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PetCreatorInterface::class, PetCreationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
