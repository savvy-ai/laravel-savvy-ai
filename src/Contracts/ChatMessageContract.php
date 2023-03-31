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
    public static function fromChatReply(ChatReplyContract $reply): self;

    public function role(Role $role = null): Role|self;

    public function content(string $content = null): string|self;
}
