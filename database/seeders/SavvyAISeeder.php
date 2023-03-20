<?php

namespace Database\Seeders;

use SavvyAI\Models\Agent;
use SavvyAI\Models\Chatbot;
use SavvyAI\Models\Dialogue;
use Illuminate\Database\Seeder;
use SavvyAI\Models\Trainable;

class SavvyAISeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trainable = Trainable::query()->create([
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

        $agents = Agent::factory()->createMany([
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
            ],
            [
                'chatbot_id' => $chatbot->id,
                'name'   => 'LoanOfficer',
                'classification' => 'If the text is a loan inquiry, you MUST say "@LoanOfficer()"',
                'prompt' => implode(PHP_EOL, [
                    'Carefully classify the text to find the correct assistant.',
                    '',
                    'You MUST classify the text according to the following rules:',
                    'If you cannot CONFIDENTLY classify the text, you MUST say "@Unknown()"',
                    '@foreach($dialogues as $dialogue)',
                    '{{ $dialogue->classification() }}',
                    '@endforeach',
                ]),
            ],
            [
                'chatbot_id' => $chatbot->id,
                'name'   => 'TimeWeather',
                'classification' => 'If the text is an inquiry about the current time or current weather, you MUST say "@TimeWeather()"',
                'prompt' => implode(PHP_EOL, [
                    'Carefully classify the text to find the correct assistant.',
                    '',
                    'You MUST classify the text according to the following rules:',
                    'If you cannot CONFIDENTLY classify the text, you MUST say "@Unknown()"',
                    '@foreach($dialogues as $dialogue)',
                    '{{ $dialogue->classification() }}',
                    '@endforeach',
                ]),
            ],
            [
                'chatbot_id' => $chatbot->id,
                'name'   => 'GeneralKnowledge',
                'classification' => 'If the text is an inquiry about the a vacation rental property and its amenities, you MUST say "@GeneralKnowledge()"',
                'prompt' => implode(PHP_EOL, [
                    'Carefully classify the text to find the correct assistant.',
                    '',
                    'You MUST classify the text according to the following rules:',
                    'If you cannot CONFIDENTLY classify the text, you MUST say "@Unknown()"',
                    '@foreach($dialogues as $dialogue)',
                    '{{ $dialogue->classification() }}',
                    '@endforeach',
                ]),
            ]
        ]);

        Dialogue::factory()->createMany([
            [
                'agent_id' => $agents[0]->id,
                'name'     => 'Greeting',
                'topic'    => 'greetings or farewells',
                'classification' => 'If the text is a greeting, you MUST say "@Greeting()"',
                'prompt'   => 'You are a vacation rental guest service assistant and your role is to respond to greetings in a friendly manner.',
            ],
        ]);

        Dialogue::factory()->createMany([
            [
                'agent_id' => $agents[1]->id,
                'name'     => 'PersonalLoan',
                'topic'    => 'personal loans',
                'classification' => 'If the text is a personal loan inquiry, you MUST say "@PersonalLoan()"',
                'prompt' => implode(PHP_EOL, [
                    'You are a personal loan officer and your role is to respond to inquiries about personal loans using the following knowledge base:',
                    '',
                    '- Personal loan applicants must have a credit score of at least 650.',
                    '- Personal loan applicants must have a monthly income of at least $3,000.',
                    '- Personal loan applicants must have a debt-to-income ratio of less than 40%.',
                    '- Personal loan applicants must have a minimum of 2 years of employment history.',
                    '- Personal loan applicants must have a minimum of 2 years of credit history.',
                    '- Personal loan applicants must have a minimum of 2 years of residency history.',
                    '',
                    'You MUST ONLY use the knowledge base above to extract an answer.',
                    'If you cannot CONFIDENTLY answer or the question does not make sense, given the knowledage base above, you MUST say "@Unknown()"'
                ]),
            ],
            [
                'agent_id' => $agents[1]->id,
                'name'     => 'CommercialLoan',
                'topic'    => 'commercial loans',
                'classification' => 'If the text is a commercial loan inquiry, you MUST say @CommercialLoan()',
                'prompt' => implode(PHP_EOL, [
                    'You are a commercial loan officer and your role is to respond to inquiries about commercial loans using the following knowledge base:',
                    '',
                    '- Commercial loan applicants must have a credit score of at least 700.',
                    '- Commercial loan applicants must have a monthly income of at least $10,000.',
                    '- Commercial loan applicants must have a debt-to-income ratio of less than 30%.',
                    '- Commercial loan applicants must have a minimum of 5 years of employment history.',
                    '- Commercial loan applicants must have a minimum of 5 years of credit history.',
                    '- Commercial loan applicants must have a minimum of 5 years of residency history.',
                    'Here is a quick summary of the time and weather at this property:',
                    '',
                    'You MUST ONLY use the knowledge base above to extract an answer.',
                    'If you cannot CONFIDENTLY answer or the question does not make sense, given the knowledge base above, you MUST say @Unknown()'
                ]),
            ],
            [
                'agent_id' => $agents[2]->id,
                'name'     => 'TimeWeather',
                'topic'    => 'current time or current weather',
                'classification' => 'If the text is an inquiry about the time, current time, weather, or current weather, you MUST say "@TimeWeather()"',
                'prompt' => implode(PHP_EOL, [
                    'Here is a quick summary of the time and weather at this property:',
                    '',
                    '<GetCurrentTime/>',
                    '<GetCurrentWeather/>',
                ]),
            ],
            [
                'agent_id' => $agents[3]->id,
                'name'     => 'GeneralKnowledgeDefault',
                'topic'    => 'vacation rental property, amenities, and location',
                'classification' => 'If the text is an inquiry about the property and its amenities, you MUST say "@GeneralKnowledgeDefault()"',
                'prompt' => implode(PHP_EOL, [
                    'You are a customer support agent trained to use the knowledge base below to answer the following question.',
                    'If the answer cannot be found, apologize and request their email for followup.',
                    '',
                    'Examples:',
                    'Question: The fridge is broken?',
                    'Answer: Sorry to hear the fridge is broken, please contact management below',
                    'Question: Why is the sky blue?',
                    'Answer: I\'m sorry I don\'t have an answer for that, please contact management below',
                    '',
                    'Knowledge Base:',
                    '<GetPropertyStatements/>',
                ]),
            ],
        ]);
    }
}
