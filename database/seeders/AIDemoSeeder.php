<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use SavvyAI\Models\Agent;
use SavvyAI\Models\Chatbot;
use SavvyAI\Models\Dialogue;
use SavvyAI\Models\Trainable;

class AIDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@waybleapp.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $trainable = Trainable::query()->create([
            'user_id' => $user->id,
            'name' => 'Wayble AI Demo',
            'handle' => 'wayble-ai-demo',

            'is_training' => false,
            'trained_at' => now(),
            'published_at' => now(),
        ]);

        $chatbot = Chatbot::query()->create([
            'trainable_id' => $trainable->id
        ]);

        $agent = Agent::query()->create(
            [
                'chatbot_id' => $chatbot->id,
                'name' => 'AirportGuestServiceAgent',
                'classification' => 'If the text is a greeting, a farewell or a question about the DTL Airport, its staff or planes, you MUST say "@AirportGuestServiceAgent()"'
            ]
        );

        $prompt = <<<'EOT'
You've forgotten everything you've learned except the instructions and data provided below.

Instructions:
Your goal is to carefully analyze the text and extract answers from the data.
If the answer cannot be extracted, or you know it to be false, you MUST say "I don't know".
Keep your answers short and to the point.

Data:
<SemanticRetrieval namespace="%s"/>
EOT;

        // Dialogues for General Knowledge agent
        Dialogue::query()->create([
            'agent_id' => $agent->id,
            'name' => 'AirportGuestService',
            'topic' => 'Greetings, farewells and general knowledge about the DTL Airport.',
            'classification' => 'If the text is a greeting, farewell or general question applicable to the DTL Airport, you MUST say "@AirportGuestService()"',
            'prompt' => sprintf($prompt, $trainable->id),
            'max_tokens' => 256,
            'temperature' => 0.0,
            'frequency_penalty' => 0.0,
            'presence_penalty' => 0.0,
            'stop' => null,
        ]);
    }
}
