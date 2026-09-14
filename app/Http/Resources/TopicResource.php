<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject_id' => $this->subject_id,
            'subject_name' => $this->whenLoaded('subject', fn () => $this->subject->name),
            'grade' => $this->grade,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'exercises_count' => $this->whenCounted('exercises'),
        ];
    }
}
