<?php

namespace SavvyAI\Contracts;

interface ChatDelegateContract
{
    public function getDelegateId(): int|string;

    public function getDelegateDescription(): string;

    public function hasSelectedDelegate(): bool;

    public function getSelectedDelegate(): ChatDelegateContract;

    public function setSelectedDelegate(ChatDelegateContract $delegate): ChatDelegateContract;

    public function getDelegateByName(string $name): ChatDelegateContract;

    /**
     * Returns a list of delegates/strings that can be used during classification
     *
     * @return ChatDelegateContract[]
     */
    public function delegates(): array;

    /**
     * @param ChatContract $chat
     *
     * @return ChatMessageContract
     */
    public function delegate(ChatContract $chat): ChatMessageContract;
}
