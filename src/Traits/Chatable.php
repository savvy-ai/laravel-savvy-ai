<?php

namespace SavvyAI\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenAI\Exceptions\ErrorException;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Contracts\ChatDelegateContract;
use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Contracts\ChatReplyContract;
use SavvyAI\Exceptions\DelegateNotFoundException;
use SavvyAI\Exceptions\OffTopicException;
use SavvyAI\Features\Chatting\ChatMessage;
use SavvyAI\Features\Chatting\Role;
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
    private array $chatReplies = [];

    /**
     * @var array
     */
    private array $chatContext = [];

    /**
     * @param ChatDelegateContract $delegate
     * @param ChatMessageContract $message
     *
     * @return ChatMessageContract|null
     * @throws Throwable
     */
    public function reply(ChatDelegateContract $delegate, ChatMessageContract $message): ?ChatMessageContract
    {
        try
        {
            return $this->replyWithoutCatching($delegate, $message);
        }
        catch (DelegateNotFoundException|OffTopicException $e)
        {
            $this->clearMessages();

            if ($delegate->hasSelectedDelegate())
            {
                $delegate->getSelectedDelegate()->resetSelectedDelegate();
                $delegate->resetSelectedDelegate();
            }

            return $this->replyWithoutCatching($delegate, $message);
        }
        catch (ErrorException $e)
        {
            Log::error($e->getMessage());

            return new ChatMessage(
                Role::Assistant,
                'Sorry, we are having issues contacting our AI service. Please try again later.',
            );
        }
        catch (Throwable $throwable)
        {
            Log::error($throwable->getMessage());

            return new ChatMessage(
                Role::Assistant,
                'Oops, something went wrong on our end. Please try again later.',
            );
        }
    }

    /**
     * @param ChatDelegateContract $delegate
     * @param ChatMessageContract $message
     *
     * @return ChatMessageContract|null
     * @throws Throwable
     */
    public function replyWithoutCatching(ChatDelegateContract $delegate, ChatMessageContract $message): ?ChatMessageContract
    {
        $this->chatMessages = [];
        $this->chatReplies = [];
        $this->chatContext = [];

        $this->addMessage($message);

        $message = $delegate->delegate($this);

        $this->addMessage($message);

        $this->persist($delegate);

        return $message;
    }

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

    public function clearMessages(): ChatContract
    {
        $this->chatMessages = [];

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
     * @param ChatDelegateContract $delegate
     *
     * @return bool
     */
    public function persist(ChatDelegateContract $delegate): bool
    {
        return true;
    }
}
