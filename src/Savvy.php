<?php

namespace SavvyAI;

/**
 * @mixin SavvyAI
 */
class Savvy extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'savvy';
    }
}
