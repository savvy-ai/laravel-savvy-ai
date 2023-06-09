<?php

namespace SavvyAI\Models;

use DateTime;
use Illuminate\Contracts\Database\Eloquent\Builder;
use SavvyAI\Features\Training\Splitter;

/**
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property string $handle
 * @property bool $is_training
 * @property DateTime $trained_at
 * @property DateTime $published_at
 *
 * @property Chat[] $chats
 * @property Chatbot $chatbot
 */
class Trainable extends Model implements \SavvyAI\Contracts\TrainableContract
{
    protected $casts = [
        'is_training'  => 'boolean',
        'trained_at'   => 'datetime',
        'published_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'handle',
        'is_training',
        'trained_at',
        'published_at',
    ];

    protected $appends = [
        'has_been_trained',
    ];

    public function getTextSplitter(): Splitter
    {
        return new Splitter();
    }

    public function getStatementRepository(): Builder
    {
        return $this->statements();
    }

    public function chatbot(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Chatbot::class);
    }

    public function chats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Chat::class);
    }

    public function statements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Statement::class);
    }

    public function getHasBeenTrainedAttribute(): bool
    {
        return $this->statements()->exists();
    }
}
