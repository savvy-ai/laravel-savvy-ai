<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property Chat $chat
 */
class Message extends Model
{
    protected $casts = [
        'is_read' => 'boolean',
        'media'   => 'array',
    ];

    protected $fillable = [
        'chat_id',
        'dialogue_id',
        'role',
        'content',
        'media',
    ];

    public function chat(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function scopeThread(Builder $query, ?Dialogue $dialogue = null)
    {
        if ($dialogue)
        {
            // Messages from a specific dialogue
            $query->where('dialogue_id', $dialogue->id);
        }

        // Messages from the last 24 hours
        $query->where('created_at', '>=', now()->subDay());

        // Messages organized by the created date
        $query->orderBy('created_at', 'asc');
    }
}
