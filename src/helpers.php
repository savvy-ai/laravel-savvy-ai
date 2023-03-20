<?php

if (!function_exists('savvy'))
{
    function savvy(): \SavvyAI\Savvy\Savvy
    {
        return app('savvy');
    }
}
