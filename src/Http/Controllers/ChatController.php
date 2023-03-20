<?php

namespace SavvyAI\Http\Controllers;

use Illuminate\Routing\Controller;
use OpenAI;
use SavvyAI\Models\Chat;
use SavvyAI\Models\Dialogue;
use SavvyAI\Models\Message;
use SavvyAI\Savvy\BaseDialogue;

class ChatController extends Controller
{
    /**
     * Delegates the given message to the appropriate chatbot to handle it
     *
     * @param Chat $chat
     * @param Message $message
     *
     * @return Message
     */
    public function chat()
    {
        $role    = request()->input('role');
        $content = request()->input('content');

        $message = [
            'role'    => $role,
            'content' => $content,
        ];

        return (new BaseDialogue())->delegate([$message]);

        // $property->load(['user', 'chatbot.agents.dialogues']);

        // $message = $property->chatbot->delegate($chat, $message);

        // $property->user->subtractCredits($property->user->tokensToCredits('{totalTokens}'))->save();

        // $message = new Message([
        //     'role'    => $role,
        //     'content' => $content,
        // ]);

        // return $message;
    }

    public function config()
    {
        return config('savvy-ai');
    }
}
