<?php

namespace SavvyAI\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SavvyAI\Contracts\ChatDelegateContract;
use SavvyAI\Contracts\ChatContract;
use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Exceptions\DelegateNotFoundException;

trait Delegatable
{
    use InteractsWithAIService;

    protected ?ChatDelegateContract $selectedDelegate;

    public function getDelegateId(): int|string
    {
        return Str::uuid();
    }

    public function getDelegateDescription(): string
    {
        return 'A delegate that helps with some task @DelegateName()';
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

    /**
     * @return ChatDelegateContract
     */
    public function resetSelectedDelegate(): ChatDelegateContract
    {
        $this->selectedDelegate = null;

        return $this;
    }

    public function getDelegateByName(string $name): ChatDelegateContract
    {
        return $this;
    }

    /**
     * @throws DelegateNotFoundException
     */
    public function delegate(ChatContract $chat): ChatMessageContract
    {
        $incomingMessage = $chat->getLastMessage();

        if (!$this->hasSelectedDelegate())
        {
            Log::debug('Bot::delegate() -> finding a suitable delegate');

            $delegates = collect($this->delegates())->map(function (ChatDelegateContract $delegate) {
                return $delegate->getDelegateDescription();
            })->toArray();

            $reply = $this->classify($incomingMessage->content(), $delegates);
            $delegate = $this->getDelegateByName($reply->extractDelegateName());

            $this->setSelectedDelegate($delegate);

            $chat->addReply($reply);
        }

        Log::debug('Bot::delegate() -> delegating to delegate: ' . get_class($this->getSelectedDelegate()));

        $outgoingMessage = $this
            ->getSelectedDelegate()
            ->delegate($chat);

        Log::debug('Bot::delegate() -> message from delegate: ' . $outgoingMessage->content());

        return $outgoingMessage;
    }
}
