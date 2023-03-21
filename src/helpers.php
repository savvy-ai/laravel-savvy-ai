<?php

if (!function_exists('savvy'))
{
    function savvy(): \SavvyAI\Savvy
    {
        return app('savvy');
    }
}

if (!function_exists('openai'))
{
    function openai(): OpenAI\Client
    {
        return app('openai');
    }
}
