<?php

namespace SavvyAI\Savvy\Chat;

enum Role: string
{
    case System    = 'system';
    case Assistant = 'assistant';
    case User      = 'user';
}
