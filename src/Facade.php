<?php

namespace SavvyAI;

/**
 * @mixin Savvy
 */
class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'savvy';
    }
}
