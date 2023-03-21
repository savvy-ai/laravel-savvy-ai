<?php

namespace SavvyAI\Console\Commands;

use SavvyAI\Models\Message;
use SavvyAI\Savvy\Chat\Role;
use Illuminate\Console\Command;
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
    public function handle()
    {
        $trainable = Trainable::where('id', $this->argument('trainable_id'))
            ->with('chatbot.agents.dialogues')
            ->firstOrFail();

        $chat = $trainable->chats()
            ->firstOrCreate(
                ['id' => $this->argument('chat_id')]
            );

        $this->info(print_r([
            'Trainable' => $trainable->id,
            'Chat' => $chat->id,
        ], true));

        while(true)
        {
            $prompt = $this->ask('User');

            $message = $trainable->chatbot->delegate($chat, new Message([
                    'role' => Role::User->value,
                    'content' => $prompt,
                ]
            ));

            $this->info(sprintf(' Savvy: %s > %s', PHP_EOL, $message->content()));
        }

        return Command::SUCCESS;
    }
}
