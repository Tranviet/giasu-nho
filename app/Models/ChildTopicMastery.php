<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildTopicMastery extends Model
{
    use HasFactory;

    protected $table = 'child_topic_mastery';

    protected $fillable = [
        'child_id',
        'topic_id',
        'mastery_score',
    ];

    protected function casts(): array
    {
        return [
            'mastery_score' => 'integer',
        ];
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
