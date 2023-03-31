<?php

namespace SavvyAI\Commands;

use Illuminate\Console\Command;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Features\Chatting\ChatMessage;
use SavvyAI\Features\Chatting\Role;
use SavvyAI\Models\Chat;
use SavvyAI\Models\Message;
use SavvyAI\Models\Trainable;

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
     */
    public function handle(): int
    {
        $trainable = Trainable::query()
            ->where('id', $this->argument('trainable_id'))
            ->with('chatbot.agents.dialogues')
            ->firstOrFail();

        /** @var ChatContract $chat */
        $chat = Chat::query()
            ->firstOrCreate([
                'id' => $this->argument('chat_id'),
                'trainable_id' => $trainable->id,
            ]);

        $this->info(print_r([
            'Trainable' => $trainable->id,
            'Chat' => $chat->id,
        ], true));

        while(true)
        {
            $prompt = $this->ask('User');

            if ($prompt === 'exit')
            {
                break;
            }

            $chat->addMessage(new ChatMessage(Role::User, $prompt));

            $message = $trainable->chatbot->delegate($chat);

            $this->info(sprintf(' Savvy: %s > %s', PHP_EOL, $message->content()));
        }

        return self::SUCCESS;
    }
}
