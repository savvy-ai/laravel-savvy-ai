<?php

namespace SavvyAI\Savvy\Chat;

class Message
{
    private Role $role;
    private string $content;
    private ?Reply $reply = null;

    public static function fromReply(Reply $reply): self
    {
        return new self(Role::from($reply->role()), $reply->content(), $reply);
    }

    public function __construct(Role $role, string $content, ?Reply $reply = null)
    {
        $this->role    = $role;
        $this->content = trim($content);
        $this->reply   = $reply;
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function reply(): Reply
    {
        return $this->reply ?? new Reply([]);
    }

    public function onTopic(): bool
    {
        return $this->reply()->onTopic();
    }

    public function toArray(): array
    {
        return [
            'role'   => $this->role(),
            'content' => $this->content(),
        ];
    }
}
