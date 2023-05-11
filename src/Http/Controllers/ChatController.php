<?php

namespace SavvyAI\Http\Controllers;

use SavvyAI\Models\Chat;
use SavvyAI\Models\Trainable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Features\Chatting\ChatMessage;
use SavvyAI\Features\Chatting\Role;

class ChatController extends Controller
{
    public function show(Request $request, Trainable $trainable): \Inertia\Response
    {
        if (empty($trainable->statements()->first()))
        {
            abort(404, 'No training found');
        }

        return Inertia::render('Chat', compact('trainable'));
    }

    public function ask()
    {
        request()->validate(['prompt' => 'required|string|max:255']);

        $chatId   = request()->post('chatId');
        $domainId = request()->post('domainId');
        $prompt   = request()->post('prompt');

        $trainable = Trainable::query()
            ->where('id', $domainId)
            ->with('chatbot.agents.dialogues')
            ->firstOrFail();

        /** @var ChatContract $chat */
        $chat = Chat::query()
            ->with(['agent', 'dialogue'])
            ->firstOrCreate([
                'id' => $chatId,
                'domain_id' => $domainId
            ]);

        $chatbot  = $trainable->chatbot;
        $agent    = $chat->agent;
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

        return [
            'reply'    => $message->content(),
            'agentId'  => $chat->agent->id ?? null,
            'chatId'   => $chat->id ?? null,
            'domainId' => $trainable->id ?? null
        ];
    }

    public function history()
    {
        $chat = Chat::query()
            ->where('id', request()->post('chatId'))
            ->first();

        if (empty($chat->messages))
        {
            return [
                'history' => []
            ];
        }

        return [
            'history' => $chat->messages
        ];
    }

    public function clear()
    {
        $chatId = request()->post('chatId');
        $chat   = Chat::query()->where('id', $chatId)->first();

        if (empty($chat))
        {
            return [
                'success' => false,
                'message' => 'Chat not found.'
            ];
        }
        
        try
        {
            $chat->clearChatHistory();

            return [
                'success' => true,
                'message' => 'Chat history cleared.'
            ];
        }
        catch (\Throwable $e)
        {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
