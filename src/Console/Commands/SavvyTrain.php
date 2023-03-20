<?php

namespace SavvyAI\Console\Commands;

use SavvyAI\Models\Property;
use SavvyAI\Models\User;
use SavvyAI\Savvy\Config\TrainingConfig;
use SavvyAI\Savvy\SavvyAI;
use Illuminate\Console\Command;

class SavvyTrain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:train {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Train model using a text file with domain knowledge.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $text  = file_get_contents($this->argument('file'));
        $savvy = new SavvyAI();

        $savvy->train($text, new TrainingConfig([
            'user' => User::first(),
            'property' => Property::first() ?? new Property(['id' => 123456]),
            'metadata' => [
                'property_id' => Property::first()->id ?? 123456,
            ],
        ]));

        $this->info('👍');

        return Command::SUCCESS;
    }
}
