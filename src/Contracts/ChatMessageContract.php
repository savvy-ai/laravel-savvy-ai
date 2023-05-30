<?php

namespace SavvyAI\Contracts;

use SavvyAI\Features\Chatting\Role;

/**
 * Represents a message to pass around during chat delegation
 *
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 *
 * @package SavvyAI\Contracts
 */
interface ChatMessageContract
{
    public static function fromChatReply(ChatReplyContract $reply): ChatMessageContract;

    public function role(): Role;

    public function content(): string;

    /**
     * @return array<string, string>
     */
    public function asArray(): array;

    /**
     * @return array<string, string>
     */
    public function asPersistable(): array;
}
