<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use SavvyAI\Exceptions\AgentNotFoundException;
use SavvyAI\Exceptions\DialogueNotFoundException;
use SavvyAI\Exceptions\OffTopicException;
use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Features\Chatting\Role;
use SavvyAI\Traits\InteractsWithAIService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use SavvyAI\Contracts\AI\DelegateContract;

/**
 * @property Trainable $trainable
 * @property Agent[] $agents
 */
class Chatbot extends Model implements DelegateContract
{
    use HasUuids;
    use HasFactory;
    use InteractsWithAIService;

    protected $fillable = [
        'trainable_id',
        'prompt',
        'model',
        'max_tokens',
        'temperature',
        'presence_penalty',
        'frequency_penalty',
        'stop'
    ];

    protected $appends = [
        'name'
    ];

    public function prepareFallbackMessage(): Message
    {
        return new Message([
            'role' => Role::Assistant->value,
            'content' => 'I am sorry, I do not understand what you are saying. Please try again.',
        ]);
    }

    public function delegates(): array
    {
        return $this->agents
            ->map(fn($agent) => $agent->classification)
            ->toArray();
    }

    public function delegate(Chat $chat, Message $incomingMessage, \Exception $previouslyThrowException = null): Message
    {
        Log::debug('Bot::delegate()');

        $reply = null;
        $agent = $previouslyThrowException ? null : $chat->agent;

        try
        {
            if (!$agent)
            {
                Log::debug('Bot::delegate() -> finding a suitable agent');

                $reply = $this->classify($incomingMessage->content, $this->delegates());

                if ($reply->isContextUnknown() || !($agent = $reply->agent()))
                {
                    throw new AgentNotFoundException($reply->content());
                }

                $agent = Agent::query()->where('name', $agent)->firstOrFail();
            }

            Log::debug('Bot::delegate() -> delegating to agent: ' . $agent->name);

            $outgoingMessage = $agent->delegate($chat, $incomingMessage, $previouslyThrowException);

            Log::debug('Bot::delegate() -> message from agent: ' . $outgoingMessage->content);

            // Finish up
            // 1. Save incoming and outgoing messages to the conversation
            $chat->messages()->save($incomingMessage);
            $chat->messages()->save($outgoingMessage);

            // 2. Save the conversation because the agent can attach itself and the dialogue
            $chat->save();

            return $outgoingMessage;
        } catch (OffTopicException|UnknownContextException $e)
        {
            Log::error($e->getMessage());

            if (!$previouslyThrowException)
            {
                return $this->delegate($chat, $incomingMessage, $e);
            }

            return $this->prepareFallbackMessage();
        } catch (AgentNotFoundException|DialogueNotFoundException $e)
        {
            Log::error($e->getMessage());

            return $this->prepareFallbackMessage();
        }
    }

    public function getNameAttribute(): string
    {
        return $this->trainable->name . ' Chatbot';
    }

    public function trainable(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Trainable::class);
    }

    public function agents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Agent::class);
    }

    public function dialogues(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Dialogue::class, Agent::class);
    }
}
