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

    public function dialogue(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Dialogue::class);
    }
}
