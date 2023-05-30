<?php

namespace SavvyAI\Features\Chatting;

use Illuminate\Support\Facades\Log;
use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Contracts\ChatReplyContract;

class ChatMessage implements ChatMessageContract
{
    protected Role $role = Role::User;
    protected string $content = '';
    protected array $media = [];

    public static function fromChatReply(ChatReplyContract $reply, ?array $media = null): ChatMessageContract
    {
        return new self(
            Role::from($reply->role()),
            $reply->content(),
            $media ?? []
        );
    }

    public function __construct(Role $role = Role::User, string $content = '', ?array $media = null)
    {
        $this->role = $role;
        $this->content = $content;
        $this->media = $media ?? [];
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

    public function relevantMedia(): array
    {
        return $this->applyRelevanceFilter($this->content, $this->media);
    }

    /**
     * Get the instance as an array
     *
     * @return array<string, string>
     */
    public function asArray(): array
    {
        return [
            'role' => $this->role->value,
            'content' => $this->content,
        ];
    }

    /**
     * Get the instance as an array for persisting
     *
     * @return array<string, string>
     */
    public function asPersistable(): array
    {
        return [
            'role' => $this->role->value,
            'content' => $this->content,
            'media' => $this->applyRelevanceFilter($this->content, $this->media),
        ];
    }

    protected function applyRelevanceFilter(string $text, ?array $media = []): array
    {
        if (empty($media))
        {
            return $media;
        }

        foreach ($media as &$value)
        {
            $value['score'] = similar_text(strtoupper($text), strtoupper($value['title']));
        }

        usort($media, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        Log::debug('Media scored', $media);

        $media = array_filter($media, function ($value) {
            return $value['score'] >= 10;
        });

        return array_slice($media, 0, 3);
    }
}
