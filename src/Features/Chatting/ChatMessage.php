<?php

namespace SavvyAI\Features\Chatting;

use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Contracts\ChatReplyContract;

class ChatMessage implements ChatMessageContract
{
    protected Role $role = Role::User;
    protected string $content = '';
    protected array $replies = [];

    public static function fromChatReply(ChatReplyContract $reply): self
    {
        $instance = new self();

        $instance->role      = Role::from($reply->role());
        $instance->content   = $reply->content();
        $instance->replies[] = $reply;

        return $instance;
    }

    public function __construct(Role $role = Role::User, string $content = '')
    {
        $this->role    = $role;
        $this->content = $content;
    }

    public function role(Role $role = null): Role|self
    {
        if ($role !== null)
        {
            $this->role = $role;

            return $this;
        }

        return $this->role;
    }

    public function content(string $content = null): string|self
    {
        if ($content !== null)
        {
            $this->content = $content;

            return $this;
        }

        return $this->content;
    }
}
