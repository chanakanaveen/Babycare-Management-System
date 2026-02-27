<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AiGrowthPredictionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AiGrowthPredictionService::class, function ($app) {
            return new AiGrowthPredictionService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
