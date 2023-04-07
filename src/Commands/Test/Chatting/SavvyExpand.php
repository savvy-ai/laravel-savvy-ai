<?php

namespace SavvyAI\Commands\Test\Chatting;

use Illuminate\Console\Command;
use SavvyAI\Traits\ExpandsPromptSnippets;

class SavvyExpand extends Command
{
    use ExpandsPromptSnippets;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:test:expand {input}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expand a prompt with snippets';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $input = $this->argument('input');

        while (true)
        {
            $prompt = $this->ask('prompt');

            $expanded = $this->expand($prompt, $input);

            $this->info($expanded);
        }
    }
}
