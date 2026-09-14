<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'grade',
        'name',
        'slug',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'grade' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    public function masteries(): HasMany
    {
        return $this->hasMany(ChildTopicMastery::class);
    }
}
