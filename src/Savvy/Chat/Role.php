<?php

namespace SavvyAI\Savvy\Chat;

enum Role: string
{
    case User      = 'user';
    case System    = 'system';
    case Assistant = 'assistant';
}
