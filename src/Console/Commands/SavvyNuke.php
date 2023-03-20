<?php

namespace SavvyAI\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Http;

class SavvyNuke extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:nuke';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets database, dumps Pinecone data, and more';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Clear pinecone data
        foreach (User::all() as $user)
        {
            Http::pinecone()->post(
                '/vectors/delete',
                [
                    'namespace' => $user->uuid,
                    'deleteAll' => true,
                ],
            )->json();
        }

        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('routes:clear');
        $this->call('migrate:fresh');
        $this->call('db:seed');

        $this->info('👍');

        return Command::SUCCESS;
    }
}
