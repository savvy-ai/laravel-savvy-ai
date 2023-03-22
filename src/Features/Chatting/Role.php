<?php

namespace SavvyAI\Features\Chatting;

enum Role: string
{
    case User      = 'user';
    case System    = 'system';
    case Assistant = 'assistant';
}
