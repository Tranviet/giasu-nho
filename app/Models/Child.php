<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Child extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'grade',
        'avatar',
    ];

    protected function casts(): array
    {
        return [
            'grade' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ExerciseSubmission::class);
    }

    public function aiInteractions(): HasMany
    {
        return $this->hasMany(AiInteraction::class);
    }

    public function topicMasteries(): HasMany
    {
        return $this->hasMany(ChildTopicMastery::class);
    }
}
