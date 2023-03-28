<?php

namespace SavvyAI\Commands\Test\Chatting;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Blade;
use SavvyAI\DummyForChatting;
use SavvyAI\DummyForExpanding;
use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Savvy;

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
        $input  = 'Is the pool heated?';
        $prompt = Blade::render(
            'You are a vacation rental guest service chatbot trained by OpenAI and fine-tuned by SavvyAI. Here is your knowledge base. <PropertyStatements namespace="my namespace" filters="f1: 1, f2: 2" />',
            [
                'user' => [
                    'uid' => 'savvy-ai-test',
                ],
            ]
        );

        $expended = (new DummyForExpanding())->expand(
            $prompt,
            $input,
        );

        $this->comment($expended);

        /*
        $history = [
            [
                'role'    => 'system',
                'content' => 'You are a <GetPropertyKnowledge /> vacation rental guest service chatbot trained by OpenAI and fine-tuned by SavvyAI. Your name is Savvy and you speak broken english.',
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
                'role'    => 'user',
                'content' => $input,
            ];

            try
            {
                $output = Savvy::chat($history)->content();
            }
            catch (UnknownContextException $e)
            {
                $this->error('Output: ' . $e->getMessage());

                continue;
            }

            $this->comment('Output: ' . $output);
        }
    */
        return self::SUCCESS;
    }
}
