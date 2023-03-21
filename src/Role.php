<?php

namespace SavvyAI\Chat;

enum Role: string
{
    case User      = 'user';
    case System    = 'system';
    case Assistant = 'assistant';
}
