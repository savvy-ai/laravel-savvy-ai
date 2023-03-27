<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use SavvyAI\Models\Chat;
use SavvyAI\Models\Message;
use SavvyAI\Exceptions\AgentNotFoundException;
use SavvyAI\Exceptions\DialogueNotFoundException;
use SavvyAI\Exceptions\OffTopicException;
use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Features\Chatting\Role;
use SavvyAI\Traits\InteractsWithAIService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use SavvyAI\Contracts\Delegatable;

/**
 * @property \SavvyAI\Models\Trainable $trainable
 * @property \SavvyAI\Models\Agent[] $agents
 */
class Chatbot extends Model implements Delegatable
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

    public function delegates(): array
    {
        return $this->agents;
    }

    public function preparePrompt()
    {
        return Blade::render($this->prompt, ['agents' => $this->agents]);
    }

    public function prepareFallbackMessage()
    {
        return new Message([
            'role' => Role::Assistant,
            'content' => 'Sorry, I am not able to help with that. Is there anything else I can help with?',
        ]);
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

                $reply = $this->call([
                    ['role' => Role::System->value, 'content' => $this->preparePrompt()],
                    ['role' => Role::User->value, 'content' => $incomingMessage->content],
                ]);

                $agent = $reply->agent();
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
        }
        catch (OffTopicException | UnknownContextException $e)
        {
            Log::error($e->getMessage());

            if (!$previouslyThrowException)
            {
                return $this->delegate($chat, $incomingMessage, $e);
            }

            return $this->prepareFallbackMessage();
        }
        catch (AgentNotFoundException | DialogueNotFoundException $e)
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
