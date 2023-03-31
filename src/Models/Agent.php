<?php

namespace SavvyAI\Models;

use Exception;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use SavvyAI\Contracts\ChatDelegateContract;
use SavvyAI\Traits\Delegatable;
use SavvyAI\Traits\InteractsWithAIService;

/**
 * @property Chatbot $bot
 * @property Dialogue[]|Collection $dialogues
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

    public function getDelegateId(): int|string
    {
        return $this->id;
    }

    public function getDelegateByName(string $name): ChatDelegateContract
    {
        return $this->dialogues->where('name', $name)->first();
    }

    public function getDelegateDescription(): string
    {
        return $this->classification;
    }

    public function delegates(): array
    {
        return $this->dialogues->all();
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
