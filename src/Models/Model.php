<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Model extends \Illuminate\Database\Eloquent\Model
{
    use HasUuids;
    use HasFactory;

    protected static function newFactory()
    {
        $name = explode('\\', get_called_class());
        $factory = sprintf('Database\\Factories\\%sFactory', array_pop($name));

        if (class_exists($factory))
        {
            return $factory::new();
        }

        return null;
    }
}
