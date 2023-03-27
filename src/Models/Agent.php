<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use SavvyAI\Exceptions\DialogueNotFoundException;
use SavvyAI\Features\Chatting\Role;
use SavvyAI\Traits\InteractsWithAIService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;

/**
 * @property \SavvyAI\Models\Chatbot $bot
 * @property \SavvyAI\Models\Dialogue[] $dialogues
 */
class Agent extends Model
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

    public function chatbot(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Chatbot::class);
    }

    public function dialogues(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Dialogue::class);
    }

    public function classification()
    {
        return $this->classification;
    }

    public function preparePrompt()
    {
        return Blade::render($this->prompt, ['dialogues' => $this->dialogues]);
    }

    public function delegate(Chat $chat, Message $incomingMessage, \Exception $previouslyThrowException = null): Message
    {
        Log::debug('Agent::delegate()');

        $reply    = null;
        $dialogue = $previouslyThrowException ? null : $chat->dialogue;

        try
        {
            if (!$dialogue)
            {
                Log::debug('Agent::delegate() -> finding a suitable dialogue');

                $reply = $this->call([
                    ['role' => Role::System->value, 'content' => $this->preparePrompt()],
                    ['role' => Role::User->value, 'content' => $incomingMessage->content],
                ]);

                if ($reply->isContextUnknown() || !($dialogue = $reply->dialogue()))
                {
                    throw new DialogueNotFoundException($reply->content());
                }
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
        catch (\Exception $e)
        {
            throw $e;
        }
    }
}
