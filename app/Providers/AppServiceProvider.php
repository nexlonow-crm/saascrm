<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Deals\Models\Deal; 
use App\Observers\DealObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Deal::observe(DealObserver::class);
    }
}
