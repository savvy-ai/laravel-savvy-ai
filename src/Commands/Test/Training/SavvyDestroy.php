<?php

namespace SavvyAI\Commands\Test\Training;

use Illuminate\Console\Command;
use SavvyAI\DummyForTraining;

class SavvyDestroy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:test:destroy {namespace=savvy-ai-test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Destroy a training namespace';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->comment(print_r((new DummyForTraining())->destroy($this->argument('namespace')), true));

        return self::SUCCESS;
    }
}
