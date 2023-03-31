<?php

namespace SavvyAI\Traits;

use Illuminate\Support\Str;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Contracts\ChatReplyContract;
use Throwable;

trait Chatable
{
    /**
     * @var ChatMessageContract[]
     */
    private array $chatMessages = [];

    /**
     * @var ChatReplyContract[]
     */
    private array $chatReplies  = [];

    /**
     * @var ?Throwable
     */
    private ?Throwable $throwable = null;

    public function getChatId(): int|string
    {
        return Str::uuid();
    }

    /**
     * @return ChatMessageContract[]
     */
    public function getChatHistory(): array
    {
        return [];
    }

    /**
     * @return ChatMessageContract[]
     */
    public function getMessages(): array
    {
        return $this->chatMessages;
    }

    public function getLastMessage(): ?ChatMessageContract
    {
        return last($this->chatMessages);
    }

    /**
     * @param ChatMessageContract $message
     *
     * @return ChatContract
     */
    public function addMessage(ChatMessageContract $message): ChatContract
    {
        $this->chatMessages[] = $message;

        return $this;
    }

    /**
     * @param ChatMessageContract[] $messages
     *
     * @return ChatContract
     */
    public function addMessages(array $messages): ChatContract
    {
        $this->chatMessages[] = array_merge($this->chatMessages, $messages);

        return $this;
    }

    public function getReplies(): array
    {
        return $this->chatReplies;
    }

    public function getLastReply(): ?ChatReplyContract
    {
        return last($this->chatReplies);
    }

    public function addReply(ChatReplyContract $reply): ChatContract
    {
        $this->chatReplies[] = $reply;

        return $this;
    }

    /**
     * @param ChatReplyContract[] $replies
     *
     * @return ChatContract
     */
    public function addReplies(array $replies): ChatContract
    {
        $this->chatReplies[] = $replies;

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
