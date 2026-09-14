<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitExerciseRequest;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\TopicResource;
use App\Models\Child;
use App\Models\ChildTopicMastery;
use App\Models\Exercise;
use App\Models\ExerciseSubmission;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class CurriculumController extends Controller
{
    /**
     * List all subjects.
     */
    public function subjects(): AnonymousResourceCollection
    {
        return SubjectResource::collection(Subject::all());
    }

    /**
     * List topics filtered by subject and grade.
     */
    public function topics(Request $request): AnonymousResourceCollection
    {
        $query = Topic::with('subject')->withCount('exercises');

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        $topics = $query->orderBy('grade')->orderBy('sort_order')->get();

        return TopicResource::collection($topics);
    }

    /**
     * Get details of a single topic.
     */
    public function topicDetail(Topic $topic): TopicResource
    {
        return new TopicResource($topic->load('subject')->loadCount('exercises'));
    }

    /**
     * List exercises for a topic.
     */
    public function exercises(Topic $topic): AnonymousResourceCollection
    {
        return ExerciseResource::collection($topic->exercises);
    }

    /**
     * Submit an answer to an exercise and auto-grade.
     */
    public function submitExercise(SubmitExerciseRequest $request, Exercise $exercise): JsonResponse
    {
        $child = Child::findOrFail($request->child_id);
        Gate::authorize('view', $child);

        $expectedAnswer = trim((string) ($exercise->content['answer'] ?? ''));
        $submittedAnswer = trim((string) $request->answer);

        $isCorrect = mb_strtolower($expectedAnswer, 'UTF-8') === mb_strtolower($submittedAnswer, 'UTF-8');
        $explanation = $exercise->content['explanation'] ?? ($isCorrect ? 'Tuyệt vời! Con đã trả lời chính xác.' : 'Chưa đúng rồi con ơi, hãy thử lại nhé!');

        $submission = ExerciseSubmission::create([
            'child_id' => $child->id,
            'exercise_id' => $exercise->id,
            'answer' => $submittedAnswer,
            'is_correct' => $isCorrect,
            'ai_feedback' => [
                'explanation' => $explanation,
                'expected_answer' => $expectedAnswer,
            ],
            'submitted_at' => now(),
        ]);

        // Calculate and update topic mastery
        $topicId = $exercise->topic_id;
        $totalExercisesInTopic = Exercise::where('topic_id', $topicId)->count();

        $correctCount = ExerciseSubmission::where('child_id', $child->id)
            ->whereHas('exercise', fn ($q) => $q->where('topic_id', $topicId))
            ->where('is_correct', true)
            ->distinct('exercise_id')
            ->count('exercise_id');

        $masteryScore = $totalExercisesInTopic > 0
            ? (int) round(($correctCount / $totalExercisesInTopic) * 100)
            : 0;

        ChildTopicMastery::updateOrCreate(
            ['child_id' => $child->id, 'topic_id' => $topicId],
            ['mastery_score' => min(100, $masteryScore)]
        );

        return response()->json([
            'is_correct' => $isCorrect,
            'explanation' => $explanation,
            'expected_answer' => $expectedAnswer,
            'submission_id' => $submission->id,
            'topic_mastery_score' => $masteryScore,
        ]);
    }
}
