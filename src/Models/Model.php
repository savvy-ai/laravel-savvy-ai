<?php

namespace SavvyAI\Models;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public static function newFactory()
    {
        $name = explode('\\', get_called_class());
        $factory = sprintf('Database\\Factories\\%sFactory', array_pop($name));

        if (class_exists($factory))
        {
            return call_user_func([$factory, 'new']);
        }

        return null;
    }
}
