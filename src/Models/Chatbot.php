<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SavvyAI\Contracts\ChatDelegateContract;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Features\Chatting\ChatMessage;
use SavvyAI\Features\Chatting\Role;
use SavvyAI\Traits\Delegatable;

/**
 * @property Trainable $trainable
 * @property Agent[] $agents
 */
class Chatbot extends Model implements ChatDelegateContract
{
    use HasUuids;
    use HasFactory;
    use Delegatable;

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

    protected $appends = ['name'];

    /**
     * @param string $name
     *
     * @return ChatDelegateContract
     */
    public function getDelegateByName(string $name): ChatDelegateContract
    {
        return Agent::query()->where('name', $name)->first();
    }

    public function delegates(): array
    {
        return $this->agents;
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
