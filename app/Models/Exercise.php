<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'type',
        'content',
        'difficulty',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'difficulty' => 'integer',
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ExerciseSubmission::class);
    }
}
