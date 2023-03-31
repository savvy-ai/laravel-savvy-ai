<?php

namespace SavvyAI\Contracts;

interface ChatContract
{
    public function getChatId(): int|string;

    /**
     * @return ChatMessageContract[]
     */
    public function getChatHistory(): array;

    /**
     * @return ChatMessageContract[]
     */
    public function getMessages(): array;

    /**
     * @return ?ChatMessageContract
     */
    public function getLastMessage(): ?ChatMessageContract;

    /**
     * @param ChatMessageContract $message
     *
     * @return ChatContract
     */
    public function addMessage(ChatMessageContract $message): ChatContract;

    /**
     * @param ChatMessageContract[] $messages
     *
     * @return ChatContract
     */
    public function addMessages(array $messages): ChatContract;

    /**
     * @return ChatReplyContract[]
     */
    public function getReplies(): array;

    /**
     * @param ChatReplyContract $reply
     *
     * @return ChatContract
     */
    public function addReply(ChatReplyContract $reply): ChatContract;

    public function getLastReply(): ?ChatReplyContract;

    /**
     * @return bool
     */
    public function persist(): bool;
}
