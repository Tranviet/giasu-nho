<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'child_id',
        'type',
        'input_summary',
        'ai_response',
        'model_used',
        'input_tokens',
        'output_tokens',
        'estimated_cost_usd',
        'status',
        'latency_ms',
    ];

    protected function casts(): array
    {
        return [
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'estimated_cost_usd' => 'float',
            'latency_ms' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }
}
