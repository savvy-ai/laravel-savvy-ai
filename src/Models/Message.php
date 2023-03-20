<?php

namespace SavvyAI\Models;

use SavvyAI\Savvy\Chat\Reply;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \SavvyAI\Models\Chat $chat
 */
class Message extends Model
{
    use HasUuids;
    use HasFactory;

    /**
     * Allows the message to tell agent if the conversation needs to remember the agent and dialogue.
     *
     * @var bool
     */
    public $persistContext   = true;
    public $formattedAsReply = true;
    public $totalTokensUsed  = 0;

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

    public function addToHistory(Reply $reply)
    {
        $this->totalTokensUsed += $reply->totalTokensUsed();
    }

    public static function fromReply(Reply $reply): self
    {
        return new Message([
            'role'    => $reply->role(),
            'content' => $reply->content(),
        ]);
    }
}
