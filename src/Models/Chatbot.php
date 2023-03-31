<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Contracts\ChatDelegateContract;
use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Traits\Delegatable;

/**
 * @property Trainable $trainable
 * @property Agent[]|Collection $agents
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

    public function delegated(ChatContract $chat, ChatMessageContract $message): void
    {
        $chat->addMessage($message);

        foreach ($chat->getMessages() as $message)
        {
            Message::query()->create([
                'chat_id' => $chat->getChatId(),
                ...$message->asArray()
            ]);
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
