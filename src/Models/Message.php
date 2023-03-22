<?php

namespace SavvyAI\Models;

use SavvyAI\Features\Chatting\Reply;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SavvyAI\Features\Chatting\Role;

/**
 * @property \SavvyAI\Models\Chat $chat
 */
class Message extends Model
{
    use HasUuids;
    use HasFactory;

    public $persistContext   = true;
    public $formattedAsReply = true;
    public $totalTokensUsed  = 0;

    public ?Reply $reply = null;

    protected $casts = [
        'is_read' => 'boolean',
        'media'   => 'array',
    ];

    protected $fillable = [
        'chat_id',
        'role',
        'content',
        'media',
    ];

    protected $appends = [
        'reply',
    ];

    public function getReplyAttribute()
    {
        return $this->reply;
    }

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

    public static function fromReply(Reply $reply): self
    {
        $message = new Message([
            'role'    => $reply->role(),
            'content' => $reply->content(),
        ]);

        $message->reply = $reply;

        return $message;
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function content(): string
    {
        return $this->content;
    }
}
