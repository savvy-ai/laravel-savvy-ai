<?php

namespace SavvyAI\Models;

use Illuminate\Support\Collection;
use SavvyAI\Contracts\ChatDelegateContract;
use SavvyAI\Traits\Delegatable;

/**
 * @property string $id
 * @property string $trainable_id
 * @property string $prompt
 * @property string $model
 * @property int $max_tokens
 * @property float $temperature
 * @property float $presence_penalty
 * @property float $frequency_penalty
 * @property string $stop
 *
 * @property Trainable $trainable
 * @property Agent[]|Collection $agents
 */
class Chatbot extends Model implements ChatDelegateContract
{
    use Delegatable;

    protected $casts = [
        'welcome_prompts' => 'array'
    ];

    protected $fillable = [
        'trainable_id',
        'welcome_message',
        'welcome_prompts'
    ];

    protected $appends = ['name'];

    public function getDelegateId(): int|string
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return ChatDelegateContract
     */
    public function getDelegateByName(string $name): ChatDelegateContract
    {
        return $this->agents->where('name', $name)->first();
    }

    public function delegates(): array
    {
        return $this->agents->all();
    }

    public function getNameAttribute(): string
    {
        return sprintf('%s Chatbot', $this->trainable->value('name'));
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
