<?php

namespace SavvyAI\Commands\Test;

use Illuminate\Console\Command;
use SavvyAI\Dummy;
use SavvyAI\Exceptions\UnknownContextException;

class SavvyClassify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:test:classify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Classification testing command loop';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        while(true)
        {
            $input = $this->ask('Input') ?? '';

            if ($input === 'exit')
            {
                break;
            }

            try
            {
                $output = (new Dummy())->classify(
                    $input,
                    [
                        'If the text is about a thing, you MUST say "@Thing()"',
                        'If the text is about a place, you MUST say "@Place()"',
                        'If the text is about a person, you MUST say "@Person()"',
                    ]
                )->content();
            }
            catch (UnknownContextException $e)
            {
                $this->error('Output: ' . $e->getMessage());

                continue;
            }

            $this->comment('Output: ' . $output);
        }

        return Command::SUCCESS;
    }
}
