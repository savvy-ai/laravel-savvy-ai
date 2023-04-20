<?php

namespace SavvyAI\Models;

use SavvyAI\Traits\InteractsWithAIService;

/**
 * @property string $id
 * @property string $trainable_id
 * @property string $statement
 *
 * @property Trainable $trainable
 */
class Statement extends Model
{
    use InteractsWithAIService;

    protected $fillable = [
        'trainable_id',
        'statement',
    ];

    protected static function booted()
    {
        static::created(function (self $statement) {
            vector()->post('/vectors/upsert', [
                'namespace' => $statement->getVectorStoreNamespace(),
                'vectors' => [
                    [
                        'id' => $statement->id,
                        'values' => $statement->getVectorValues(),
                    ],
                ],
            ]);
        });

        static::updated(function(self $statement) {
            vector()->post('/vectors/upsert', [
                'namespace' => $statement->getVectorStoreNamespace(),
                'vectors' => [
                    [
                        'id' => $statement->id,
                        'values' => $statement->getVectorValues(),
                    ],
                ],
            ]);
        });

        static::deleted(function(self $statement) {
            vector()->post('/vectors/delete', [
                'namespace' => $statement->getVectorStoreNamespace(),
                'ids' => [$statement->id],
            ]);
        });
    }

    /**
     * @return string
     */
    public function getVectorStoreNamespace(): string
    {
        return $this->trainable->id;
    }

    public function getVectorValues(): array
    {
        return $this->vectorize($this->statement);
    }

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
