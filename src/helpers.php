<?php

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\App;

if (!function_exists('savvy'))
{
    function savvy(): \SavvyAI\Savvy
    {
        return App::make('savvy');
    }
}

if (!function_exists('openai'))
{
    function openai(): OpenAI\Client
    {
        return App::make('openai');
    }
}

if (!function_exists('pinecone'))
{
    function pinecone(): PendingRequest
    {
        return App::make('pinecone');
    }
}
