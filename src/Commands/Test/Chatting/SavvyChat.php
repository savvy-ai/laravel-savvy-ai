<?php

namespace SavvyAI\Commands\Test\Chatting;

use Illuminate\Console\Command;
use SavvyAI\DummyForChatting;
use SavvyAI\Exceptions\UnknownContextException;

class SavvyChat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:test:chat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a chat session in a loop';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $dummy = new DummyForChatting(['stop' => null, 'maxTokens' => 256]);

        $history = [
            [
                'role' => 'system',
                'content' => 'You are a vacation rental guest service chatbot trained by OpenAI and fine-tuned by SavvyAI. Your name is Savvy and you speak broken english.',
            ]
        ];

        while (true)
        {
            $input = $this->ask('Input') ?? '';


            if ($input === 'exit')
            {
                break;
            }

            $history[] = [
                'role' => 'user',
                'content' => $input,
            ];

            try
            {
                $output = $dummy
                    ->chat($history)
                    ->content();
            }
            catch (UnknownContextException $e)
            {
                $this->error('Output: ' . $e->getMessage());

                continue;
            }

            $this->comment('Output: ' . $output);
        }

        return self::SUCCESS;
    }
}
