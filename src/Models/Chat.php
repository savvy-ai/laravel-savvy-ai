<?php

namespace SavvyAI\Models;

use SavvyAI\Models\Agent;
use SavvyAI\Models\Dialogue;
use SavvyAI\Models\Trainable;
use SavvyAI\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \SavvyAI\Models\Trainable $trainable
 * @property \SavvyAI\Models\Message[] $messages
 * @property \SavvyAI\Models\Agent $agent
 * @property \SavvyAI\Models\Dialogue $dialogue
 */
class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'handle',
        'trainable_id',
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
