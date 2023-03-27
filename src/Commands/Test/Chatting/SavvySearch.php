<?php

namespace SavvyAI\Commands\Test\Chatting;

use Illuminate\Console\Command;
use SavvyAI\DummyForChatting;

class SavvySearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:test:search {namespace=savvy-ai-test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search vectorized sentences';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        while(true)
        {
            $dummy   = new DummyForChatting(['stop' => null, 'maxTokens' => 256]);
            $input   = $this->ask('Input') ?? '';
            $vector  = $dummy->vectorize($input);
            $results = $dummy->search($vector, $this->argument('namespace'));

            if ($input === 'exit')
            {
                break;
            }

            $this->comment(print_r($results, true));
        }

        return self::SUCCESS;
    }
}
