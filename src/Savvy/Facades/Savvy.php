<?php

namespace SavvyAI\Savvy\Facades;

/**
 * @mixin \SavvyAI\Savvy\Savvy
 */
class Savvy extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'savvy';
    }
}
