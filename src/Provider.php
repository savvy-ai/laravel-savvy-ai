<?php

namespace SavvyAI;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use OpenAI;
use OpenAI\Client;
use SavvyAI\Exceptions\MissingApiKeyException;

class Provider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SavvyAI::class, static fn () => new SavvyAI());
        $this->app->singleton(Client::class, static function () {
            $driver = config('savvy-ai.drivers.ai');

            $key = config(sprintf('savvy-ai.%s.key', $driver));
            $org = config(sprintf('savvy-ai.%s.org', $driver));

            if (! is_string($key) || ($org !== null && ! is_string($org)))
            {
                throw MissingApiKeyException::create();
            }

            return OpenAI::client($key, $org);
        });
        $this->app->singleton('pinecone', static function () {
            return Http::withHeaders([
                'Api-Key' => Config::get('savvy-ai.pinecone.key'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->baseUrl(Config::get('savvy-ai.pinecone.url'));
        });

        $this->app->alias(SavvyAI::class, 'savvy');
        $this->app->alias(Client::class, 'openai');
    }

    public function provides(): array
    {
        return [
            Client::class,
            SavvyAI::class,
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
                \SavvyAI\Commands\Test\Chatting\SavvyClassify::class,
                \SavvyAI\Commands\Test\Chatting\SavvyValidate::class,
                \SavvyAI\Commands\Test\Chatting\SavvyChat::class,
                \SavvyAI\Commands\Test\Chatting\SavvySearch::class,
                \SavvyAI\Commands\Test\Training\SavvyIndex::class,
                \SavvyAI\Commands\Test\Training\SavvyDestroy::class,
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

        $this->publishes([
            __DIR__.'/../filament/Resources' => app_path('Filament/Resources'),
        ], 'savvy-ai-filament');
    }
}
