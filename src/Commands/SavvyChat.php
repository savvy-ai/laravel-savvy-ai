<?php

namespace SavvyAI\Commands;

use Illuminate\Console\Command;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Features\Chatting\ChatMessage;
use SavvyAI\Features\Chatting\Role;
use SavvyAI\Models\Chat;
use SavvyAI\Models\Trainable;
use Throwable;

class SavvyChat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:chat {trainable_id : Trainable ID (uuid)} {chat_id? : Optional Chat ID (uuid)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chat with Savvy using the latest GPT-3.5 model';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Throwable
     */
    public function handle(): int
    {
        /** @var Trainable $trainable */
        $trainable = Trainable::query()
            ->where('id', $this->argument('trainable_id'))
            ->with('chatbot.agents.dialogues')
            ->firstOrFail();

        /** @var ChatContract $chat */
        $chat = Chat::query()
            ->with(['agent', 'dialogue'])
            ->firstOrCreate([
                'id' => $this->argument('chat_id'),
                'trainable_id' => $trainable->id,
            ]);

        $this->info(print_r([
            'Trainable' => $trainable->id,
            'Chat' => $chat->id,
        ], true));

        while (true)
        {
            $prompt = $this->ask('User');

            if ($prompt === 'exit')
            {
                break;
            }

            $chatbot = $trainable->chatbot;
            $agent = $chat->agent;
            $dialogue = $chat->dialogue;

            if ($agent && $dialogue)
            {
                $agent->setSelectedDelegate($dialogue);
                $chatbot->setSelectedDelegate($agent);
            }


            try
            {
                $message = $chat->reply($chatbot, new ChatMessage(Role::User, $prompt));
            }
            catch (\Throwable $e)
            {
                $message = new ChatMessage(Role::Assistant, $e->getMessage());
            }

            // $message = $chat->reply($trainable->chatbot, new ChatMessage(Role::User, $prompt));

            $this->info(sprintf(' Savvy: %s > %s%s %s', PHP_EOL, $message->content(), PHP_EOL, print_r($message->media(), true)));
        }

        return self::SUCCESS;
    }
}
