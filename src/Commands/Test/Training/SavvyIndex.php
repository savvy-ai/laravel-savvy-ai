<?php

namespace SavvyAI\Commands\Test\Training;

use Illuminate\Console\Command;
use SavvyAI\DummyForTraining;

class SavvyIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:test:index {file} {namespace=savvy-ai-test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index text in a file into sentences that will can be used as knowledge for chat';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $dummy = new DummyForTraining();

        $file = $this->argument('file') ?? '';

        if (!is_readable($file))
        {
            $this->error('File is not readable');

            return static::FAILURE;
        }

        $text = file_get_contents($file);

        $sentences = $dummy->summarizeForTraining($text, 128, 512);
        $vectors   = $dummy->vectorizeForStorage($sentences);
        $memorized = $dummy->store($vectors, $this->argument('namespace'));

        $this->comment(print_r($memorized, true));

        // $this->comment(print_r(collect($vectors)->map(fn($vector) => [$vector['id'], $vector['sentence']])->toArray(), true));

        return self::SUCCESS;
    }
}
