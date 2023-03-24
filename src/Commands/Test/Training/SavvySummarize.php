<?php

namespace SavvyAI\Commands\Test\Training;

use Illuminate\Console\Command;
use SavvyAI\DummyForTraining;
use SavvyAI\Exceptions\UnknownContextException;

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
        $file = $this->argument('file') ?? '';

        if (!is_readable($file))
        {
            $this->error('File is not readable');

            return Command::FAILURE;
        }

        $text = file_get_contents($file);

        try
        {
            $reply = (new DummyForTraining(['maxTokens' => 2000]))->summarize($text);
        }
        catch (UnknownContextException $e)
        {
            $this->error('Output: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $this->comment('Tokens used: '. $reply->totalTokensUsed());
        $this->comment(print_r(explode(PHP_EOL, $reply->content()), true));

        return Command::SUCCESS;
    }
}
