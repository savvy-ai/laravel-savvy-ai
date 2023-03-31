<?php

namespace SavvyAI\Contracts;

interface ChatContract
{
    /**
     * @return ChatMessageContract[]
     */
    public function getMessages(): array;

    /**
     * @return ChatMessageContract
     */
    public function getLastMessage(): ChatMessageContract;

    /**
     * @param ChatMessageContract $message
     *
     * @return ChatContract
     */
    public function addMessage(ChatMessageContract $message): self;

    /**
     * @return ChatReplyContract[]
     */
    public function getReplies(): array;

    /**
     * @param ChatReplyContract $reply
     *
     * @return ChatContract
     */
    public function addReply(ChatReplyContract $reply): self;

    /**
     * @return bool
     */
    public function persist(): bool;
}
