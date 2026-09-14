<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProgressController extends Controller
{
    /**
     * Get a child's overall learning progress, mastery scores, and submission history.
     */
    public function show(Request $request, Child $child): JsonResponse
    {
        Gate::authorize('view', $child);

        $submissions = $child->submissions();
        $totalSubmissions = $submissions->count();
        $correctSubmissions = (clone $submissions)->where('is_correct', true)->count();
        $accuracyRate = $totalSubmissions > 0
            ? (int) round(($correctSubmissions / $totalSubmissions) * 100)
            : 0;

        $topicMasteries = $child->topicMasteries()
            ->with(['topic.subject'])
            ->get()
            ->map(function ($mastery) {
                return [
                    'topic_id' => $mastery->topic_id,
                    'topic_name' => $mastery->topic->name,
                    'grade' => $mastery->topic->grade,
                    'subject_code' => $mastery->topic->subject->code,
                    'subject_name' => $mastery->topic->subject->name,
                    'mastery_score' => $mastery->mastery_score,
                ];
            });

        $recentSubmissions = $child->submissions()
            ->with('exercise.topic')
            ->latest('submitted_at')
            ->take(5)
            ->get()
            ->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'type' => $sub->exercise_id ? 'exercise' : 'homework_photo',
                    'topic' => $sub->exercise?->topic?->name,
                    'is_correct' => $sub->is_correct,
                    'submitted_at' => $sub->submitted_at?->toIso8601String(),
                ];
            });

        return response()->json([
            'child' => [
                'id' => $child->id,
                'name' => $child->name,
                'grade' => $child->grade,
                'avatar' => $child->avatar,
            ],
            'stats' => [
                'total_submissions' => $totalSubmissions,
                'correct_submissions' => $correctSubmissions,
                'accuracy_rate' => $accuracyRate,
            ],
            'topic_masteries' => $topicMasteries,
            'recent_submissions' => $recentSubmissions,
        ]);
    }
}
