<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Collection;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Features\Chatting\ChatMessage;
use SavvyAI\Features\Chatting\Role;
use SavvyAI\Models\Agent;
use SavvyAI\Models\Dialogue;
use SavvyAI\Models\Trainable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SavvyAI\Traits\Chatable;

/**
 * @property Trainable $trainable
 * @property Message[]|Collection $messages
 * @property Agent $agent
 * @property Dialogue $dialogue
 */
class Chat extends Model implements ChatContract
{
    use HasUuids;
    use HasFactory;
    use Chatable;

    protected $fillable = [
        'handle',
        'trainable_id',
        'agent_id',
        'dialogue_id',
    ];

    public function getMessages(): array
    {
        $history = $this->messages
            ->map(function (Message $message) {
                return new ChatMessage(
                    Role::from($message->role),
                    $message->content,
                );
            })
            ->toArray();

        $history[] = $this->getLastMessage();

        return $history;
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
