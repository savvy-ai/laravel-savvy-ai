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

    public function persist(ChatDelegateContract $delegate): bool
    {
        // Save messages to history
        foreach ($this->getMessages() as $message)
        {
            $this->messages()->create($message->asArray());
        }

        // Save delegates to chat context
        $this->agent_id = $delegate->getSelectedDelegate()->id ?? null;
        $this->dialogue_id = $delegate->getSelectedDelegate()->getSelectedDelegate()->id ?? null;

        $this->save();

        return true;
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
