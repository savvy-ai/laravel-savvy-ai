<?php

namespace SavvyAI\Models;

use Exception;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use SavvyAI\Contracts\ChatDelegateContract;
use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Exceptions\OffTopicException;
use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Features\Chatting\ChatMessage;
use SavvyAI\Features\Chatting\Role;
use SavvyAI\Traits\ExpandsPromptSnippets;
use SavvyAI\Traits\InteractsWithAIService;

/**
 * @property Agent $agent
 */
class Dialogue extends Model implements ChatDelegateContract
{
    use HasUuids;
    use HasFactory;
    use InteractsWithAIService;
    use ExpandsPromptSnippets;

    protected $fillable = [
        'agent_id',
        'name',
        'prompt',
        'topic',
        'classification',
        'model',
        'max_tokens',
        'temperature',
        'presence_penalty',
        'frequency_penalty',
        'stop'
    ];


    public function delegates(): array
    {
        return [];
    }

    public function chatbot(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->agent->chatbot();
    }

    public function agent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * @param Chat $chat
     * @param ChatMessageContract $incomingMessage
     * @param ?Exception $previouslyThrownException
     * @return ChatMessageContract
     *
     * @throws OffTopicException|UnknownContextException
     */
    public function delegate(Chat $chat, ChatMessageContract $incomingMessage, Exception $previouslyThrownException = null): ChatMessageContract
    {
        Log::debug('Dialogue::delegate()');

        Log::debug('Dialogue::delegate() -> expanding prompt snippets');

        $prompt = $this->expand($this->prompt, $incomingMessage->content());

        Log::debug('Dialogue::delegate() -> generating reply');


        $messages = $chat->messages()
            ->thread($this)
            ->get()
            ->map(fn($message) => ['role' => $message->role, 'content' => $message->content]);

        $reply = $this->chat([
            ['role' => Role::System->value, 'content' => $prompt],
            ...$messages->toArray(),
            ['role' => Role::User->value, 'content' => $incomingMessage->content()],
        ]);

        $outgoingMessage = ChatMessage::fromChatReply($reply);

        $this->maxTokens = 16;
        $this->temperature = 0.0;

        Log::debug('Dialogue::delegate() -> validating reply to ensure it is on topic');

        $this->validateWithMessages([
            ['role' => Role::User->value, 'content' => $incomingMessage->content()],
            ['role' => Role::Assistant->value, 'content' => $outgoingMessage->content()],
        ], $this->topic);

        Log::debug('Dialogue::delegate() -> reply is on topic');

        Event::dispatch('chat.message-sent', ['dialogue_id' => $this->id]);

        // $outgoingMessage->dialogue_id = $this->id;

        return $outgoingMessage;
    }
}
