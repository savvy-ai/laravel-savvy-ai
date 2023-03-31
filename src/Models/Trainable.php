<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Chat[] $chats
 * @property Chatbot $chatbot
 */
class Trainable extends Model
{
    use HasUuids;
    use HasFactory;

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
