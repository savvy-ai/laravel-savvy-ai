<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statement extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainable_id',
        'text',
    ];

    public function trainable(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Trainable::class);
    }

    public function isDistinct(): bool
    {
        return !static::query()
            ->where('statement', 'like', sprintf('%%%s%%', $this->statement))
            ->exists();
    }
}
