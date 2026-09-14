<?php

namespace App\Services;

use App\Models\Exercise;
use App\Models\Topic;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;

    protected string $model;

    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY', env('GEMINI_KEY', ''));
        $this->model = config('services.gemini.model') ?? env('GEMINI_MODEL', 'gemini-3.6-flash');
    }

    /**
     * Generate exercises for a given topic using Gemini API.
     *
     * @param  string  $difficulty  'easy', 'medium', 'hard', or 'all'
     */
    public function generateExercises(Topic $topic, int $count = 5, string $difficulty = 'all'): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('GEMINI_API_KEY chưa được cấu hình trong file .env');
        }

        $subjectName = $topic->subject?->name ?? 'Toán';
        $grade = $topic->grade ?? 4;
        $topicName = $topic->name;
        $topicDesc = $topic->description ?? '';

        $prompt = <<<PROMPT
Bạn là chuyên gia sư phạm tiểu học Việt Nam, nắm vững chương trình Giáo dục phổ thông (GDPT 2018 - bộ sách Kết Nối Tri Thức / Cánh Diều).
Hãy tạo đúng {$count} bài tập trắc nghiệm 4 lựa chọn (A, B, C, D) cho học sinh Tiểu học theo thông tin sau:
- Môn học: {$subjectName}
- Khối lớp: Lớp {$grade}
- Chủ đề: "{$topicName}"
- Mô tả chi tiết chủ đề: "{$topicDesc}"
- Yêu cầu độ khó: {$difficulty}

Yêu cầu định dạng:
Trả về DUY NHẤT một mảng JSON (Array of Objects), không có markdown bọc ngoài, mỗi object gồm các trường:
1. "question": Nội dung câu hỏi rõ ràng, gần gũi với lứa tuổi học sinh lớp {$grade}. Có thể là toán đố thực tế hoặc câu hỏi ngữ pháp/từ loại Tiếng Việt.
2. "difficulty": Độ khó (1 là Dễ, 2 là Vừa, 3 là Thử thách/Khó).
3. "options": Đối tượng gồm 4 đáp án đúng chuẩn:
   {
     "A": "nội dung đáp án A",
     "B": "nội dung đáp án B",
     "C": "nội dung đáp án C",
     "D": "nội dung đáp án D"
   }
4. "answer": Ký tự của đáp án đúng ("A", "B", "C" hoặc "D").
5. "explanation": Lời giải thích ngắn gọn, dễ hiểu, dùng ngôn từ động viên vui vẻ, kèm biểu tượng ngộ nghĩnh (🌟, 🚀, 💡, 👏) giúp bé hiểu tại sao đáp án đó lại đúng.
PROMPT;

        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::timeout(60)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url, [
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'temperature' => 0.7,
                ],
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]);

        if (! $response->successful()) {
            Log::error('Gemini API Error: '.$response->body());
            throw new Exception('Lỗi khi gọi Gemini API: '.($response->json('error.message') ?? $response->status()));
        }

        $body = $response->json();
        $rawText = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';

        $cleanJson = trim($rawText);
        if (str_starts_with($cleanJson, '```json')) {
            $cleanJson = substr($cleanJson, 7);
        }
        if (str_ends_with($cleanJson, '```')) {
            $cleanJson = substr($cleanJson, 0, -3);
        }
        $cleanJson = trim($cleanJson);

        $parsed = json_decode($cleanJson, true);
        if (! is_array($parsed)) {
            Log::error('Gemini Invalid JSON Output: '.$rawText);
            throw new Exception('Gemini không trả về đúng định dạng JSON danh sách bài tập.');
        }

        return $parsed;
    }

    /**
     * Generate and save exercises directly into database.
     *
     * @return int Number of exercises created
     */
    public function generateAndSave(Topic $topic, int $count = 5, string $difficulty = 'all'): int
    {
        $exercises = $this->generateExercises($topic, $count, $difficulty);
        $savedCount = 0;

        foreach ($exercises as $item) {
            if (empty($item['question']) || empty($item['options']) || empty($item['answer'])) {
                continue;
            }

            $diff = (int) ($item['difficulty'] ?? 1);
            if ($diff < 1 || $diff > 3) {
                $diff = 1;
            }

            Exercise::create([
                'topic_id' => $topic->id,
                'type' => 'multiple_choice',
                'difficulty' => $diff,
                'content' => [
                    'question' => $item['question'],
                    'options' => $item['options'],
                    'answer' => strtoupper(trim($item['answer'])),
                    'explanation' => $item['explanation'] ?? '',
                ],
            ]);

            $savedCount++;
        }

        return $savedCount;
    }
}
