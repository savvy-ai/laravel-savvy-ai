<?php

namespace SavvyAI\Models;

use Exception;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use SavvyAI\Contracts\AI\DelegateContract;
use SavvyAI\Exceptions\OffTopicException;
use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Features\Chatting\Role;
use SavvyAI\Traits\ExpandsPromptSnippets;
use SavvyAI\Traits\InteractsWithAIService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * @property Agent $agent
 */
class Dialogue extends Model implements DelegateContract
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
     * @param Message $incomingMessage
     * @param ?Exception $previouslyThrownException
     * @return Message
     *
     * @throws OffTopicException|UnknownContextException
     */
    public function delegate(Chat $chat, Message $incomingMessage, Exception $previouslyThrownException = null): Message
    {
        Log::debug('Dialogue::delegate()');

        Log::debug('Dialogue::delegate() -> expanding prompt snippets');

        $prompt = $this->expand($this->prompt, $incomingMessage->content);

        Log::debug('Dialogue::delegate() -> generating reply');

        $messages = $chat->messages()
            ->thread($this)
            ->get()
            ->map(fn($message) => ['role' => $message->role, 'content' => $message->content]);

        $reply = $this->chat([
            ['role' => Role::System->value, 'content' => $prompt],
            ...$messages->toArray(),
            ['role' => Role::User->value, 'content' => $incomingMessage->content],
        ]);

        $outgoingMessage = Message::fromReply($reply);

        $this->maxTokens = 16;
        $this->temperature = 0.0;

        Log::debug('Dialogue::delegate() -> validating reply to ensure it is on topic');

        $this->validateWithMessages([
            ['role' => Role::User->value, 'content' => $incomingMessage->content],
            ['role' => Role::Assistant->value, 'content' => $outgoingMessage->content],
        ], $this->topic);

        Log::debug('Dialogue::delegate() -> reply is on topic');

        $outgoingMessage->dialogue_id = $this->id;

        return $outgoingMessage;
    }
}
