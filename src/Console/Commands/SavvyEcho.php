<?php

namespace SavvyAI\Console\Commands;

use Illuminate\Console\Command;

class SavvyEcho extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:echo {phrase : The phrase to echo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a phrase to SavvyAI to echo it back to you';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->comment($this->argument('phrase'));

        return Command::SUCCESS;
    }
}
