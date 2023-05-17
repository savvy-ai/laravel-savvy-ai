<?php

namespace SavvyAI\Features\Chatting;

use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Contracts\ChatReplyContract;

class ChatMessage implements ChatMessageContract
{
    protected Role $role = Role::User;
    protected string $content = '';
    protected array $media = [];

    public static function fromChatReply(ChatReplyContract $reply): ChatMessageContract
    {
        return new self(
            Role::from($reply->role()),
            $reply->content(),
            $reply->media(),
        );
    }

    public function __construct(Role $role = Role::User, string $content = '', ?array $media = null)
    {
        $this->role    = $role;
        $this->content = $content;
        $this->media   = $media ?? [];
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function media(): array
    {
        return $this->media;
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
            'media'   => $this->media,
        ];
    }
}
