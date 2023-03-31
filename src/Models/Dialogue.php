<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Contracts\ChatDelegateContract;
use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Exceptions\OffTopicException;
use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Features\Chatting\ChatMessage;
use SavvyAI\Features\Chatting\Role;
use SavvyAI\Traits\Delegatable;
use SavvyAI\Traits\ExpandsPromptSnippets;
use SavvyAI\Traits\InteractsWithAIService;

/**
 * @property Agent $agent
 */
class Dialogue extends Model implements ChatDelegateContract
{
    use HasUuids;
    use HasFactory;
    use Delegatable;
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

    public function getDelegateDescription(): string
    {
        return $this->classification;
    }

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

    public function delegate(ChatContract $chat): ChatMessageContract
    {
        $incomingMessage = $chat->getLastMessage();

        Log::debug('Dialogue::delegate()');

        Log::debug('Dialogue::delegate() -> expanding prompt snippets');

        $prompt = $this->expand($this->prompt, $incomingMessage->content());

        Log::debug('Dialogue::delegate() -> generating reply');

        $reply = $this->chat([
            new ChatMessage(Role::System, $prompt),
            ...$chat->getMessages(),
        ]);

        $chat->addReply($reply);

        $outgoingMessage = ChatMessage::fromChatReply($reply);

//        $this->maxTokens   = 16;
//        $this->temperature = 0.0;
//
//        Log::debug('Dialogue::delegate() -> validating reply to ensure it is on topic');
//
//        $this->validateWithMessages([
//            $incomingMessage->toArray(),
//            $outgoingMessage->toArray(),
//        ], $this->topic);
//
//        Log::debug('Dialogue::delegate() -> reply is on topic');
//
//        Event::dispatch('chat.message-sent', ['dialogue_id' => $this->id]);

        // $outgoingMessage->dialogue_id = $this->id;

        return $outgoingMessage;
    }
}
