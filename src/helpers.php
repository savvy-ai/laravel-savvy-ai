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

if (!function_exists('ai'))
{
    function ai(): OpenAI\Client
    {
        return App::make('ai');
    }
}

if (!function_exists('vector'))
{
    function vector(): PendingRequest
    {
        return App::make('vector');
    }
}
