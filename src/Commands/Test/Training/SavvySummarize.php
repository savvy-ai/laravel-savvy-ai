<?php

namespace SavvyAI\Commands\Test\Training;

use Illuminate\Console\Command;
use SavvyAI\DummyForTraining;

class SavvySummarize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:test:summarize {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Summarize text in a file into topical phrases';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dummy = new DummyForTraining();

        $file = $this->argument('file') ?? '';

        if (!is_readable($file))
        {
            $this->error('File is not readable');

            return Command::FAILURE;
        }

        $text = file_get_contents($file);

        $sentences = $dummy->summarize($text, 128, 512);
        $vectors   = $dummy->vectorize($sentences);
        $memorized = $dummy->memorize($vectors, 'savvy-ai-test', ['test' => 'test']);

        $this->comment(print_r($memorized, true));

        // $this->comment(print_r(collect($vectors)->map(fn($vector) => [$vector['id'], $vector['sentence']])->toArray(), true));

        return Command::SUCCESS;
    }
}
