<?php

namespace SavvyAI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SavvyAI\Models\Chat;
use SavvyAI\Models\Message;
use SavvyAI\Models\Trainable;
use SavvyAI\Chat\Role;

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
    public function chat(Request $request)
    {
        $trainable = Trainable::where('id', $request->input('trainable_id'))
            ->with('chatbot.agents.dialogues')
            ->firstOrFail();

        $chat = $trainable->chats()
            ->firstOrCreate(
                ['id' => $request->input('chat_id')]
            );

        $incomingMessage = new Message([
            'role'    => Role::User->value,
            'content' => $request->input('content'),
        ]);

        $outgoingMessage = $trainable->chatbot->delegate($chat, $incomingMessage);

        return $outgoingMessage;
    }
}
