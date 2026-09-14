<?php

namespace App\Console\Commands;

use App\Models\Topic;
use App\Services\GeminiService;
use Exception;
use Illuminate\Console\Command;

class GenerateExercisesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exercise:generate
                            {topic? : ID or slug of the topic}
                            {--count=5 : Number of exercises to generate}
                            {--difficulty=all : Difficulty level (easy, medium, hard, all)}
                            {--grade= : Filter topics by grade when generating all}
                            {--all : Generate for all topics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate curriculum-aligned exercises using Google Gemini API';

    /**
     * Execute the console command.
     */
    public function handle(GeminiService $geminiService): int
    {
        $topicInput = $this->argument('topic');
        $count = (int) $this->option('count');
        $difficulty = (string) $this->option('difficulty');
        $isAll = (bool) $this->option('all');
        $gradeFilter = $this->option('grade');

        if (! $topicInput && ! $isAll) {
            $topics = Topic::with('subject')->orderBy('grade')->orderBy('subject_id')->get();
            if ($topics->isEmpty()) {
                $this->error('Chưa có chủ đề nào trong cơ sở dữ liệu. Hãy chạy seeder trước!');

                return self::FAILURE;
            }

            $choices = [];
            foreach ($topics as $t) {
                $choices[$t->id] = "[Lớp {$t->grade}] {$t->subject->name}: {$t->name}";
            }

            $selectedId = $this->choice('Chọn chủ đề muốn sinh bài tập bằng Gemini:', $choices);
            $topic = Topic::findOrFail(array_search($selectedId, $choices));
            $topicsToProcess = collect([$topic]);
        } elseif ($isAll) {
            $query = Topic::with('subject');
            if ($gradeFilter) {
                $query->where('grade', (int) $gradeFilter);
            }
            $topicsToProcess = $query->get();
        } else {
            $topic = is_numeric($topicInput)
                ? Topic::with('subject')->find($topicInput)
                : Topic::with('subject')->where('slug', $topicInput)->first();

            if (! $topic) {
                $this->error("Không tìm thấy chủ đề với ID hoặc slug: {$topicInput}");

                return self::FAILURE;
            }

            $topicsToProcess = collect([$topic]);
        }

        $this->info("Bắt đầu sinh bài tập với mô hình Gemini ({$topicsToProcess->count()} chủ đề)...");

        $totalSaved = 0;

        foreach ($topicsToProcess as $topic) {
            $this->line("👉 Đang gọi Gemini sinh {$count} bài tập cho: [Lớp {$topic->grade}] {$topic->name}...");

            try {
                $saved = $geminiService->generateAndSave($topic, $count, $difficulty);
                $totalSaved += $saved;
                $this->info("   ✅ Đã lưu thành công {$saved} bài tập mới vào DB!");
            } catch (Exception $e) {
                $this->error('   ❌ Thất bại: '.$e->getMessage());
            }
        }

        $this->newLine();
        $this->info("🎉 Hoàn tất! Đã tạo tổng cộng {$totalSaved} câu hỏi mới trong hệ thống.");

        return self::SUCCESS;
    }
}
