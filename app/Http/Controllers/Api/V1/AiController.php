<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeworkCheckRequest;
use App\Http\Requests\QaRequest;
use App\Models\Child;
use App\Models\ExerciseSubmission;
use App\Services\ClaudeService;
use App\Services\ImageService;
use App\Services\QuotaService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class AiController extends Controller
{
    /**
     * AI Homework Check from an uploaded photo.
     */
    public function checkHomework(
        HomeworkCheckRequest $request,
        ImageService $imageService,
        ClaudeService $claudeService,
        QuotaService $quotaService
    ): JsonResponse {
        $user = $request->user();
        $child = Child::findOrFail($request->child_id);
        Gate::authorize('view', $child);

        // 1. Check user's remaining subscription quota
        if (! $quotaService->hasQuota($user)) {
            return response()->json([
                'message' => 'Bạn đã sử dụng hết hạn mức AI trong tháng. Vui lòng nâng cấp gói cước để tiếp tục.',
                'quota' => $quotaService->getQuotaSummary($user),
            ], 402);
        }

        try {
            // 2. Process, compress and store image
            $processedImage = $imageService->processAndStore($request->file('image'));

            // 3. Call Claude Vision to grade and generate friendly feedback
            $result = $claudeService->checkHomework($user, $child, $processedImage, $request->note);

            // 4. Save to submissions history
            $submission = ExerciseSubmission::create([
                'child_id' => $child->id,
                'exercise_id' => null, // Photo uploaded homework
                'image_path' => $processedImage['path'],
                'answer' => null,
                'is_correct' => null,
                'ai_feedback' => [
                    'overall_feedback' => $result['overall_feedback'],
                    'items' => $result['items'],
                ],
                'submitted_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'submission_id' => $submission->id,
                'overall_feedback' => $result['overall_feedback'],
                'items' => $result['items'],
                'image_url' => $processedImage['url'],
                'quota_remaining' => $quotaService->getRemainingQuota($user),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi xử lý và chấm bài qua AI. Vui lòng thử lại sau giây lát.',
                'error_detail' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * AI Q&A Chat for children.
     */
    public function askQuestion(
        QaRequest $request,
        ClaudeService $claudeService,
        QuotaService $quotaService
    ): JsonResponse {
        $user = $request->user();
        $child = Child::findOrFail($request->child_id);
        Gate::authorize('view', $child);

        // 1. Check user's remaining subscription quota
        if (! $quotaService->hasQuota($user)) {
            return response()->json([
                'message' => 'Bạn đã sử dụng hết hạn mức AI trong tháng. Vui lòng nâng cấp gói cước để tiếp tục.',
                'quota' => $quotaService->getQuotaSummary($user),
            ], 402);
        }

        try {
            $result = $claudeService->askQuestion($user, $child, $request->question);

            return response()->json([
                'status' => 'success',
                'answer' => $result['answer'],
                'quota_remaining' => $quotaService->getRemainingQuota($user),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Không thể kết nối với gia sư AI lúc này. Vui lòng thử lại sau.',
                'error_detail' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
