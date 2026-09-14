<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'exercise_id',
        'image_path',
        'answer',
        'is_correct',
        'ai_feedback',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'ai_feedback' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}
