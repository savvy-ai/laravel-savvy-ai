<?php

namespace SavvyAI\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $id
 * @property string $trainable_id
 * @property string $statement
 *
 * @property Trainable $trainable
 */
class Statement extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'trainable_id',
        'statement',
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
