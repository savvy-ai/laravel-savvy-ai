<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use SavvyAI\Exceptions\OffTopicException;
use SavvyAI\Savvy\Chat\Role;
use SavvyAI\Traits\InteractsWithOpenAI;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * @property \SavvyAI\Models\Agent $agent
 */
class Dialogue extends Model
{
    use HasUuids;
    use HasFactory;
    use InteractsWithOpenAI;

    public function agent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function topic()
    {
        return $this->topic;
    }

    public function classification()
    {
        return $this->classification;
    }

    public function preparePrompt()
    {
        return $this->prompt;
    }

    public function prepareTopicGuard()
    {
        return implode(PHP_EOL, [
            'Carefully analyze the following conversation to determine whether or not it is on topic.',
            'The topic of the conversation is: ' . $this->topic,
            'If the conversation is on topic, you MUST say @OnTopic()',
            'If the conversation is off topic, you MUST say @OffTopic()',
        ]);
    }

    public function delegate(Chat $chat, Message $incomingMessage): Message
    {
        Log::debug('Dialogue::delegate()');

        try
        {
            if (($message = $this->useTools($chat, $incomingMessage)))
            {
                Log::debug('Dialogue::delegate() -> generating reply with tools');

                if ($message->formattedAsReply)
                {
                    $message->dialogue_id    = $this->id;
                    $message->persistContext = false;

                    return $message;
                }

                $this->prompt = $message->content;
            }

            Log::debug('Dialogue::delegate() -> generating reply');

            $messages = $chat->messages()
                ->thread($this)
                ->get()
                ->map(fn ($message) => ['role' => $message->role, 'content' => $message->content]);

            $reply = $this->call([
                ['role' => Role::System->value, 'content' => $this->preparePrompt()],
                ...$messages->toArray(),
                ['role' => Role::User->value, 'content' => $incomingMessage->content],
            ]);

            $outgoingMessage = Message::fromReply($reply);

            $this->stop        = ' ';
            $this->temperature = 0.0;

            $reply = $this->call([
                ['role' => Role::System->value, 'content' => $this->prepareTopicGuard()],
                ['role' => Role::User->value, 'content' => $incomingMessage->content],
                ['role' => Role::Assistant->value, 'content' => $outgoingMessage->content],
            ]);

            if ($reply->onTopic())
            {
                Log::debug('Dialogue::delegate() -> reply is on topic');

                $outgoingMessage->dialogue_id = $this->id;

                return $outgoingMessage;
            }

            throw new OffTopicException($reply->content());
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    public function useTools(Chat $chat, Message $incomingMessage): ?Message
    {
        // Check if the prompt uses tools
        preg_match('/<([a-zA-Z0-9]+)\/>/', $this->prompt, $matches);

        if (!$matches)
        {
            return null;
        }

        $outgoingMessage = new Message([
            'role'    => Role::Assistant->value,
            'media'   => null,
            'content' => '',
        ]);

        $content = preg_replace_callback('/<([a-zA-Z0-9]+)\/>/', function ($matches) use ($chat, $outgoingMessage) {
            $name   = '\\SavvyAI\\Savvy\\Chat\\Tools\\' . $matches[1];
            $tool   = new $name();
            $output = $tool->use($chat, $outgoingMessage);

            if (!$output->formattedAsReply)
            {
                $outgoingMessage->formattedAsReply = false;
            }

            $outgoingMessage->media = array_merge($outgoingMessage->media ?? [], $output->media ?? []);

            return $output->content;
        }, $this->prompt);

        $outgoingMessage->content = $content;

        return $outgoingMessage;
    }
}
