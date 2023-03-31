<?php

namespace SavvyAI\Traits;

use SavvyAI\Contracts\ChatContract;
use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Contracts\ChatReplyContract;
use Throwable;

trait Chatable
{
    /**
     * @var ChatMessageContract[]
     */
    private array $messages = [];

    /**
     * @var ChatReplyContract[]
     */
    private array $replies  = [];

    /**
     * @var ?Throwable
     */
    private ?Throwable $throwable = null;

    /**
     * @return ChatMessageContract[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }


    public function getLastMessage(): ?ChatMessageContract
    {
        return last($this->messages);
    }

    /**
     * @param ChatMessageContract $message
     *
     * @return ChatContract
     */
    public function addMessage(ChatMessageContract $message): ChatContract
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @param ChatMessageContract[] $messages
     *
     * @return ChatContract
     */
    public function addMessages(array $messages): ChatContract
    {
        $this->messages[] = array_merge($this->messages, $messages);

        return $this;
    }

    public function getReplies(): array
    {
        return $this->replies;
    }

    public function addReply(ChatReplyContract $reply): ChatContract
    {
        $this->replies[] = $reply;

        return $this;
    }

    /**
     * @param ChatReplyContract[] $replies
     *
     * @return ChatContract
     */
    public function addReplies(array $replies): ChatContract
    {
        $this->replies[] = $replies;

        return $this;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return isset($this->throwable);
    }

    public function persist(): bool
    {
        return true;
    }
}
