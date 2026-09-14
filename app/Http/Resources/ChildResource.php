<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChildResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'grade' => $this->grade,
            'avatar' => $this->avatar,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
