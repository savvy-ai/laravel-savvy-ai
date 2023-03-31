<?php

namespace SavvyAI\Models;

use Exception;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Contracts\ChatDelegateContract;
use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Exceptions\DialogueNotFoundException;
use SavvyAI\Exceptions\OffTopicException;
use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Traits\Delegatable;
use SavvyAI\Traits\InteractsWithAIService;

/**
 * @property Chatbot $bot
 * @property Dialogue[] $dialogues
 */
class Agent extends Model implements ChatDelegateContract
{
    use HasUuids;
    use HasFactory;
    use Delegatable;
    use InteractsWithAIService;

    protected $fillable = [
        'chatbot_id',
        'name',
        'prompt',
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
        return $this->dialogues
            ->map(fn($dialogue) => $dialogue->classification)
            ->toArray();
    }

    public function chatbot(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Chatbot::class);
    }

    public function dialogues(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Dialogue::class);
    }
}
