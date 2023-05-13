<?php

namespace SavvyAI\Commands;

use Illuminate\Console\Command;
use SavvyAI\Models\Trainable;
use SavvyAI\Savvy;

class SavvyTrain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:train {file} {namespace=savvy-ai-test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index text in a file into sentences that will be used as knowledge for chat';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $text   = file_get_contents($this->argument('file'));
        $stored =  Savvy::trainInBatches(Trainable::query()->firstOrFail(), $text, $this->argument('namespace'));

        if (!$stored)
        {
            $this->error('Training failed');

            return self::FAILURE;
        }

        $this->info('Training completed');

        return self::SUCCESS;
    }
}
