<?php

namespace SavvyAI\Traits;

use Illuminate\Support\Facades\Log;
use SavvyAI\Contracts\ChatDelegateContract;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Contracts\ChatMessageContract;
use Throwable;

trait Delegatable
{
    use InteractsWithAIService;

    protected ?ChatDelegateContract $selectedDelegate;

    public function getHumanId(): string
    {
        return '';
    }

    public function getDelegateId(): string
    {
        return '';
    }

    public function getHumanName(): string
    {
        return '';
    }

    public function getDelegateName(): string
    {
        return sprintf('@%s()', $this->getHumanName());
    }

    public function getHumanDescription(): string
    {
        return '';
    }

    public function getDelegateDescription(): string
    {
        return '';
    }

    /**
     * @return ChatDelegateContract
     */
    public function getSelectedDelegate(): ChatDelegateContract
    {
        return $this->selectedDelegate;
    }

    /**
     * @return bool
     */
    public function hasSelectedDelegate(): bool
    {
        return isset($this->selectedDelegate);
    }

    /**
     * @param ChatDelegateContract $delegate
     *
     * @return ChatDelegateContract
     */
    public function setSelectedDelegate(ChatDelegateContract $delegate): ChatDelegateContract
    {
        $this->selectedDelegate = $delegate;

        return $this;
    }

    public function delegate(ChatContract $chat): ChatMessageContract
    {
        $incomingMessage = $chat->getLastMessage();

        try
        {
            if (!$this->hasSelectedDelegate())
            {
                Log::debug('Bot::delegate() -> finding a suitable agent');

                $reply    = $this->classify($incomingMessage->content()  , $this->delegates());
                $delegate = $this->getDelegateByName($reply->delegate());

                $this->setSelectedDelegate($delegate);
            }

            Log::debug('Bot::delegate() -> delegating to agent: ' . get_class($this->getSelectedDelegate()));

            $outgoingMessage = $this
                ->getSelectedDelegate()
                ->delegate($chat);

            Log::debug('Bot::delegate() -> message from agent: ' . $outgoingMessage->content());

            // Finish up
            // 1. Save incoming and outgoing messages to the conversation
            // $chat->messages()->save($incomingMessage);
            // $chat->messages()->save($outgoingMessage);

            // 2. Save the conversation because the agent can attach itself and the dialogue
            // $chat->save();

            $chat->addMessage($outgoingMessage)->persist();

            return $outgoingMessage;
        } catch (Throwable $throwable)
        {
            Log::error($throwable->getMessage());

            return $chat->getLastMessage();
        }
    }
}
