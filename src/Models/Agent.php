<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use SavvyAI\Contracts\AI\DelegateContract;
use SavvyAI\Exceptions\DialogueNotFoundException;
use SavvyAI\Exceptions\OffTopicException;
use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Traits\InteractsWithAIService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * @property Chatbot $bot
 * @property Dialogue[] $dialogues
 */
class Agent extends Model implements DelegateContract
{
    use HasUuids;
    use HasFactory;
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

    /**
     * @param Chat $chat
     * @param Message $incomingMessage
     * @param ?Exception $previouslyThrowException
     *
     * @return Message
     *
     * @throws DialogueNotFoundException
     * @throws OffTopicException
     * @throws UnknownContextException
     */
    public function delegate(Chat $chat, Message $incomingMessage, Exception $previouslyThrowException = null): Message
    {
        Log::debug('Agent::delegate()');

        $dialogue = $previouslyThrowException ? null : $chat->dialogue;

        if (!$dialogue)
        {
            Log::debug('Agent::delegate() -> finding a suitable dialogue');

            $reply = $this->classify($incomingMessage->content, $this->delegates());


            if (!($dialogue = $reply->dialogue()))
            {
                throw new DialogueNotFoundException($reply->content());
            }

            $dialogue = Dialogue::query()->where('name', $dialogue)->firstOrFail();
        }

        Log::debug('Agent::delegate() -> delegating to dialogue: ' . $dialogue->name);

        $outgoingMessage = $dialogue->delegate($chat, $incomingMessage);

        Log::debug('Agent::delegate() -> message from dialogue: ' . $outgoingMessage->content);

        if ($outgoingMessage->persistContext)
        {
            $chat->agent()->associate($this);
            $chat->dialogue()->associate($dialogue);
        }

        return $outgoingMessage;
    }
}
