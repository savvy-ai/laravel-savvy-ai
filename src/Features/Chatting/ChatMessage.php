<?php

namespace SavvyAI\Features\Chatting;

use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Contracts\ChatReplyContract;

class ChatMessage implements ChatMessageContract
{
    protected Role $role = Role::User;
    protected string $content = '';

    public static function fromChatReply(ChatReplyContract $reply): ChatMessageContract
    {
        return new self(
            Role::from($reply->role()),
            $reply->content(),
        );
    }

    public function __construct(Role $role = Role::User, string $content = '')
    {
        $this->role    = $role;
        $this->content = $content;
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function content(): string
    {
        return $this->content;
    }

    /**
     * Get the instance as an array
     *
     * @return array<string, string>
     */
    public function asArray(): array
    {
        return [
            'role'    => $this->role->value,
            'content' => $this->content,
        ];
    }
}
