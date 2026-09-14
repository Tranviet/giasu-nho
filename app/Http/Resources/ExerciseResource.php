<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $content = $this->content ?? [];
        // Hide answer & explanation from question list to prevent cheating
        unset($content['answer'], $content['explanation']);

        return [
            'id' => $this->id,
            'topic_id' => $this->topic_id,
            'type' => $this->type,
            'content' => $content,
            'difficulty' => $this->difficulty,
        ];
    }
}
