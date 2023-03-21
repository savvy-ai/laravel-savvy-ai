<?php

namespace SavvyAI;

/**
 * @mixin \SavvyAI\Savvy
 */
class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'savvy';
    }
}
