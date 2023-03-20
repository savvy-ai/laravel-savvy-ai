<?php

namespace SavvyAI\Savvy;

use SavvyAI\Traits\InteractsWithOpenAI;

class BaseDialogue
{
    use InteractsWithOpenAI;

    public $model = 'gpt-3.5-turbo';
    public $max_tokens = 100;
    public $temperature = 0.5;
    public $presence_penalty = 0.0;
    public $frequency_penalty = 0.0;
    public $prompt = 'You are a vacation rental guest service assistant and your role is to respond to greetings in a friendly manner.';

    public function delegate(array $messages = [])
    {
        $messages = array_merge([
            'role'    => 'system',
            'content' => $this->prompt
        ], $messages);

        return $this->call($messages);
    }
}
