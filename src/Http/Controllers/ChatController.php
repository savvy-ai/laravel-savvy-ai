<?php

namespace SavvyAI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Features\Chatting\ChatMessage;
use SavvyAI\Features\Chatting\Role;
use SavvyAI\Models\Chat;
use SavvyAI\Models\Trainable;

class ChatController extends Controller
{
    public function show(Trainable $trainable): \Inertia\Response
    {
         if (empty($trainable->statements()->first()))
         {
             Log::warning('No statements found for trainable: ' . $trainable->id);
         }

        return Inertia::render('DemoChat', compact('trainable'));
    }

    public function ask(Request $request): array
    {
        $request->validate(['prompt' => 'required|string|max:255']);

        $prompt = $request->input('prompt');
        $chatId = $request->input('chatId');
        $trainableId = $request->input('domainId');

        $trainable = Trainable::query()
            ->where('id', $trainableId)
            ->with('chatbot.agents.dialogues')
            ->firstOrFail();

        /** @var ChatContract $chat */
        $chat = Chat::query()
            ->with(['agent', 'dialogue'])
            ->firstOrCreate([
                'id' => $chatId,
                'trainable_id' => $trainableId
            ]);

        $chatbot = $trainable->chatbot;
        $agent = $chat->agent;
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
            'reply' => $message->content(),
            'agentId' => $chat->agent->id ?? null,
            'chatId' => $chat->id ?? null,
            'domainId' => $trainable->id ?? null
        ];
    }

    public function history(Request $request): array
    {
        $chat = Chat::query()
            ->where('id', $request->input('chatId'))
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
}
