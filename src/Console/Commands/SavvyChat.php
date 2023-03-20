<?php

namespace SavvyAI\Console\Commands;

use SavvyAI\Savvy\Chat\Chat;
use SavvyAI\Savvy\Chat\Message;
use SavvyAI\Savvy\Chat\Role;
use Illuminate\Console\Command;

class SavvyChat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:chat {--m|model=gpt-3.5-turbo} {--temp=1} {--max=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chat with Savvy using the latest GPT-3 model';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $chat = new Chat();

        while (true)
        {
            $prompt = $this->ask('You');

            $message = $chat->send(new Message(
                Role::User,
                $prompt,
            ));

            $this->info(sprintf(' Savvy: (%s)%s > %s', $message->topical() ? 'On Topic' : 'Off Topic', PHP_EOL, $message->content()));
        }

        return Command::SUCCESS;
    }
}
