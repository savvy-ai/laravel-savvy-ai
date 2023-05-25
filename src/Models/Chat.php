<?php

namespace SavvyAI\Models;

use Illuminate\Support\Collection;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Contracts\ChatDelegateContract;
use SavvyAI\Features\Chatting\ChatMessage;
use SavvyAI\Features\Chatting\Role;
use SavvyAI\Traits\Chatable;

/**
 * @property Trainable $trainable
 * @property Message[]|Collection $messages
 * @property Agent $agent
 * @property Dialogue $dialogue
 */
class Chat extends Model implements ChatContract
{
    use Chatable;

    protected $fillable = [
        'handle',
        'trainable_id',
        'agent_id',
        'dialogue_id',
    ];

    public function getChatId(): int|string
    {
        return $this->id;
    }

    public function getChatHistory(): array
    {
        return $this->messages
            ->map(function (Message $message) {
                return new ChatMessage(
                    Role::from($message->role),
                    $message->content,
                );
            })
            ->toArray();
    }

    /**
     * @param ChatDelegateContract $delegate
     *
     * @return bool
     */
    public function persist(ChatDelegateContract $delegate): bool
    {
        $agentId = $delegate->getSelectedDelegate()->id ?? null;
        $dialogueId = $delegate->getSelectedDelegate()->getSelectedDelegate()->id ?? null;

        // Save messages to history
        foreach ($this->getMessages() as $message)
        {
            $message = $message->asPersistable();

            $message['dialogue_id'] = $dialogueId;

            $this->messages()->create($message);
        }

        // Save delegates to chat context
        $this->agent_id = $agentId;
        $this->dialogue_id = $dialogueId;

        return $this->save();
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function trainable(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Trainable::class);
    }

    public function agent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function dialogue(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Dialogue::class);
    }
}
