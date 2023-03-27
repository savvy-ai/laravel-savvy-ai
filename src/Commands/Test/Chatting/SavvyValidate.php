<?php

namespace SavvyAI\Commands\Test\Chatting;

use Illuminate\Console\Command;
use SavvyAI\DummyForChatting;
use SavvyAI\Exceptions\UnknownContextException;

class SavvyValidate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:test:validate {text} {topic}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate text against a topic';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $text = $this->argument('text') ?? '';
        $topic = $this->argument('topic') ?? '';

        try
        {
            $output = (new DummyForChatting())->validate(
                $text,
                $topic,
            )->content();
        }
        catch (UnknownContextException $e)
        {
            $this->error('Output: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->comment('Output: ' . $output);

        return self::SUCCESS;
    }
}
