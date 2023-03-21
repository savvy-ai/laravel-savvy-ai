<?php

if (!function_exists('savvy'))
{
    function savvy(): \SavvyAI\Savvy\Savvy
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
