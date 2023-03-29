<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use SavvyAI\Models\Agent;
use SavvyAI\Models\Dialogue;
use SavvyAI\Models\Trainable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Trainable $trainable
 * @property Message[] $messages
 * @property Agent $agent
 * @property Dialogue $dialogue
 */
class Chat extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'handle',
        'trainable_id',
        'agent_id',
        'dialogue_id',
    ];

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
