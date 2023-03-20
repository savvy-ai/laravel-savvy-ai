<?php

namespace Database\Seeders;

use SavvyAI\Models\Agent;
use SavvyAI\Models\Chatbot;
use SavvyAI\Models\Dialogue;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;
use SavvyAI\Models\Trainable;

class SavvyAISeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->create([
            'name' => 'Savvy AI',
            'email' => 'selvin@savvyai.com',
            'password' => bcrypt('password'),
        ]);

        $trainable = Trainable::query()->create([
            'user_id' => $user->id,
            'name' => 'Savvy Beach House',
            'handle' => 'savvy-beach-house',
        ]);

        $chatbot = Chatbot::query()->create([
            'trainable_id' => $trainable->id,
            'prompt' => implode(PHP_EOL, [
                'Carefully classify the text to find the correct assistant.',
                '',
                'You MUST classify the text according to the following rules:',
                'If you cannot CONFIDENTLY classify the text, you MUST say "@Unknown()"',
                '@foreach($agents as $agent)',
                '{{ $agent->classification() }}',
                '@endforeach',
            ]),
        ]);

        $agent = Agent::query()->create(
            [
                'chatbot_id' => $chatbot->id,
                'name'   => 'Greeter',
                'classification' => 'If the text is a greeting, you MUST say "@Greeter()"',
                'prompt' => implode(PHP_EOL, [
                    'Carefully classify the text to find the correct assistant.',
                    '',
                    'You MUST classify the text according to the following rules:',
                    'If you cannot CONFIDENTLY classify the text, you MUST say "@Unknown()"',
                    '@foreach($dialogues as $dialogue)',
                    '{{ $dialogue->classification() }}',
                    '@endforeach',
                ]),
            ]);

            // [
            //     'chatbot_id' => $chatbot->id,
            //     'name'   => 'LoanOfficer',
            //     'classification' => 'If the text is a loan inquiry, you MUST say "@LoanOfficer()"',
            //     'prompt' => implode(PHP_EOL, [
            //         'Carefully classify the text to find the correct assistant.',
            //         '',
            //         'You MUST classify the text according to the following rules:',
            //         'If you cannot CONFIDENTLY classify the text, you MUST say "@Unknown()"',
            //         '@foreach($dialogues as $dialogue)',
            //         '{{ $dialogue->classification() }}',
            //         '@endforeach',
            //     ]),
            // ],
            // [
            //     'chatbot_id' => $chatbot->id,
            //     'name'   => 'TimeWeather',
            //     'classification' => 'If the text is an inquiry about the current time or current weather, you MUST say "@TimeWeather()"',
            //     'prompt' => implode(PHP_EOL, [
            //         'Carefully classify the text to find the correct assistant.',
            //         '',
            //         'You MUST classify the text according to the following rules:',
            //         'If you cannot CONFIDENTLY classify the text, you MUST say "@Unknown()"',
            //         '@foreach($dialogues as $dialogue)',
            //         '{{ $dialogue->classification() }}',
            //         '@endforeach',
            //     ]),
            // ],
            // [
            //     'chatbot_id' => $chatbot->id,
            //     'name'   => 'GeneralKnowledge',
            //     'classification' => 'If the text is an inquiry about the a vacation rental property and its amenities, you MUST say "@GeneralKnowledge()"',
            //     'prompt' => implode(PHP_EOL, [
            //         'Carefully classify the text to find the correct assistant.',
            //         '',
            //         'You MUST classify the text according to the following rules:',
            //         'If you cannot CONFIDENTLY classify the text, you MUST say "@Unknown()"',
            //         '@foreach($dialogues as $dialogue)',
            //         '{{ $dialogue->classification() }}',
            //         '@endforeach',
            //     ]),
            // ]
        // ]);

        Dialogue::query()->create([
                'agent_id' => $agent->id,
                'name'     => 'Greeting',
                'topic'    => 'greetings or farewells',
                'classification' => 'If the text is a greeting, you MUST say "@Greeting()"',
                'prompt'   => 'You are a vacation rental guest service assistant and your role is to respond to greetings in a friendly manner.',
        ]);
    }
}
