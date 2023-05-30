<?php

namespace SavvyAI;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use OpenAI;
use OpenAI\Client;
use SavvyAI\Exceptions\MissingApiKeyException;

class Provider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SavvyAI::class, static fn() => new SavvyAI());

        $this->app->singleton(Client::class, static function () {
            $driver = Config::get('savvy-ai.drivers.ai');

            $key = Config::get(sprintf('savvy-ai.%s.key', $driver));
            $org = Config::get(sprintf('savvy-ai.%s.org', $driver));

            if (!is_string($key) || ($org !== null && !is_string($org)))
            {
                throw MissingApiKeyException::create();
            }

            return OpenAI::client($key, $org);
        });

        $this->app->singleton('vector', static function () {
            return Http::withHeaders([
                'Api-Key' => Config::get('savvy-ai.pinecone.key'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->baseUrl(Config::get('savvy-ai.pinecone.url'));
        });

        $this->app->alias(SavvyAI::class, 'savvy');
        $this->app->alias(Client::class, 'ai');
    }

    public function provides(): array
    {
        return [
            Client::class,
            SavvyAI::class,
        ];
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerCommands();
        $this->registerPublishing();
    }

    public function registerRoutes(): void
    {
        $this->loadRoutesFrom(sprintf('%s/../routes/web.php', __DIR__));
    }

    public function registerCommands(): void
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
                \SavvyAI\Commands\Test\Chatting\SavvyExpand::class,
                \SavvyAI\Commands\Test\Training\SavvyIndex::class,
                \SavvyAI\Commands\Test\Training\SavvyDestroy::class,
            ]);
        }
    }

    public function registerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../config/savvy-ai.php' => config_path('savvy-ai.php'),
        ], 'savvy-ai-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
            __DIR__ . '/../database/seeders' => database_path('seeders')
        ], 'savvy-ai-database');

        $this->publishes([
            __DIR__ . '/../filament/Resources' => app_path('Filament/Resources'),
            __DIR__ . '/../resources/views/filament' => resource_path('views/filament'),
            __DIR__ . '/../resources/views/vendor' => resource_path('views/vendor'),
        ], 'savvy-ai-filament');

        $this->publishes([
            __DIR__ . '/../resources/js' => resource_path('js'),
        ], 'savvy-ai-js');

        $this->publishes([
            __DIR__ . '/../public' => public_path(),
        ], 'savvy-ai-public');
    }
}
