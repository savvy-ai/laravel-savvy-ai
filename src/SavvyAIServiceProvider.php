<?php

namespace SavvyAI;

use Illuminate\Support\ServiceProvider;

class SavvyAIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('savvy', fn () => new \SavvyAI\Savvy\Savvy());
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config/savvy-ai.php' => config_path('savvy-ai.php'),
        ], 'savvy-ai-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'savvy-ai-migrations');

        $this->publishes([
            __DIR__.'/../database/seeders' => database_path('seeders'),
        ], 'savvy-ai-seeders');

        if ($this->app->runningInConsole())
        {
            $this->commands([
                \SavvyAI\Console\Commands\SavvyEcho::class,
                \SavvyAI\Console\Commands\SavvyChat::class,
                \SavvyAI\Console\Commands\SavvyTrain::class,
            ]);
        }
    }
}
