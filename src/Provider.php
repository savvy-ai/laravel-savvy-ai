<?php

namespace SavvyAI;

use OpenAI;
use OpenAI\Client;
use SavvyAI\Exceptions\MissingApiKeyException;
use SavvyAI\Savvy;

class Provider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Savvy::class, static fn () => new \SavvyAI\Savvy());
        $this->app->singleton(Client::class, static function () {
            $key = config('savvy-ai.openai.key');
            $org = config('savvy-ai.openai.org');

            if (! is_string($key) || ($org !== null && ! is_string($org)))
            {
                throw MissingApiKeyException::create();
            }

            return OpenAI::client($key, $org);
        });

        $this->app->alias(Savvy::class, 'savvy');
        $this->app->alias(Client::class, 'openai');
    }

    public function provides()
    {
        return [
            Savvy::class,
            Client::class,
        ];
    }

    public function boot()
    {
        $this->registerPublishing();
        $this->registerRoutes();
        $this->registerCommands();
    }

    public function registerCommands()
    {
        if ($this->app->runningInConsole())
        {
            $this->commands([
                \SavvyAI\Commands\SavvyChat::class,
                \SavvyAI\Commands\SavvyTrain::class,
            ]);
        }
    }

    public function registerRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }

    public function registerPublishing()
    {
        $this->publishes([
            __DIR__ . '/../config/savvy-ai.php' => config_path('savvy-ai.php'),
        ], 'savvy-ai-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'savvy-ai-migrations');
    }
}
