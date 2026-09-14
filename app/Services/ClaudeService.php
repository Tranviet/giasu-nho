<?php

namespace App\Services;

use App\Models\AiInteraction;
use App\Models\Child;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    protected string $apiKey;
    protected string $model;
    protected string $haikuModel;
    protected string $apiUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key') ?? env('ANTHROPIC_API_KEY', '');
        $this->model = config('services.anthropic.model') ?? env('ANTHROPIC_MODEL', 'claude-3-7-sonnet-20250219');
        $this->haikuModel = config('services.anthropic.haiku_model') ?? env('ANTHROPIC_HAIKU_MODEL', 'claude-3-5-haiku-20241022');
    }

    /**
     * Check and grade homework from an image.
     */
    public function checkHomework(User $user, Child $child, array $imageData, ?string $note = null): array
    {
        $startTime = microtime(true);

        $systemPrompt = $this->buildHomeworkSystemPrompt($child);

        $userPromptText = "Đây là ảnh bài tập về nhà của bé {$child->name}, đang học Lớp {$child->grade}.";
        if (!empty($note)) {
            $userPromptText .= "\nLưu ý thêm từ phụ huynh: " . $note;
        }
        $userPromptText .= "\nHãy đọc kỹ bài làm của bé trong ảnh, chấm điểm từng câu và trả lời DUY NHẤT một chuỗi JSON hợp lệ theo đúng cấu trúc đã hướng dẫn.";

        $messages = [
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'image',
                        'source' => [
                            'type' => 'base64',
                            'media_type' => $imageData['mime_type'] ?? 'image/jpeg',
                            'data' => $imageData['base64'],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'text' => $userPromptText,
                    ],
                ],
            ],
        ];

        try {
            $response = $this->callAnthropicApi($messages, $systemPrompt, $this->model, 2500);
            $latencyMs = (int) round((microtime(true) - $startTime) * 1000);

            $rawContent = $response['content'][0]['text'] ?? '';
            $inputTokens = $response['usage']['input_tokens'] ?? 0;
            $outputTokens = $response['usage']['output_tokens'] ?? 0;
            $cost = $this->calculateCost($this->model, $inputTokens, $outputTokens);

            $parsedData = $this->parseStructuredJson($rawContent);

            // Log successful interaction
            AiInteraction::create([
                'user_id' => $user->id,
                'child_id' => $child->id,
                'type' => 'homework_check',
                'input_summary' => "Chấm bài chụp ảnh của bé {$child->name} (Lớp {$child->grade})" . ($note ? ": $note" : ""),
                'ai_response' => $rawContent,
                'model_used' => $this->model,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'estimated_cost_usd' => $cost,
                'status' => 'success',
                'latency_ms' => $latencyMs,
            ]);

            return [
                'success' => true,
                'overall_feedback' => $parsedData['overall_feedback'] ?? 'Gia sư đã chấm xong bài của con!',
                'items' => $parsedData['items'] ?? [],
                'tokens' => [
                    'input' => $inputTokens,
                    'output' => $outputTokens,
                ],
            ];
        } catch (Exception $e) {
            $latencyMs = (int) round((microtime(true) - $startTime) * 1000);

            Log::error('Claude API homework check failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'child_id' => $child->id,
            ]);

            AiInteraction::create([
                'user_id' => $user->id,
                'child_id' => $child->id,
                'type' => 'homework_check',
                'input_summary' => "Lỗi chấm bài chụp ảnh của bé {$child->name}",
                'ai_response' => 'Error: ' . $e->getMessage(),
                'model_used' => $this->model,
                'input_tokens' => 0,
                'output_tokens' => 0,
                'estimated_cost_usd' => 0,
                'status' => 'failed',
                'latency_ms' => $latencyMs,
            ]);

            throw $e;
        }
    }

    /**
     * Ask a question (text-only Q&A for children).
     */
    public function askQuestion(User $user, Child $child, string $question): array
    {
        $startTime = microtime(true);

        $systemPrompt = $this->buildQaSystemPrompt($child);

        $messages = [
            [
                'role' => 'user',
                'content' => $question,
            ],
        ];

        try {
            $response = $this->callAnthropicApi($messages, $systemPrompt, $this->model, 1000);
            $latencyMs = (int) round((microtime(true) - $startTime) * 1000);

            $answer = $response['content'][0]['text'] ?? '';
            $inputTokens = $response['usage']['input_tokens'] ?? 0;
            $outputTokens = $response['usage']['output_tokens'] ?? 0;
            $cost = $this->calculateCost($this->model, $inputTokens, $outputTokens);

            AiInteraction::create([
                'user_id' => $user->id,
                'child_id' => $child->id,
                'type' => 'qa_chat',
                'input_summary' => $question,
                'ai_response' => $answer,
                'model_used' => $this->model,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'estimated_cost_usd' => $cost,
                'status' => 'success',
                'latency_ms' => $latencyMs,
            ]);

            return [
                'success' => true,
                'answer' => $answer,
                'tokens' => [
                    'input' => $inputTokens,
                    'output' => $outputTokens,
                ],
            ];
        } catch (Exception $e) {
            $latencyMs = (int) round((microtime(true) - $startTime) * 1000);

            Log::error('Claude API Q&A failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'child_id' => $child->id,
            ]);

            AiInteraction::create([
                'user_id' => $user->id,
                'child_id' => $child->id,
                'type' => 'qa_chat',
                'input_summary' => $question,
                'ai_response' => 'Error: ' . $e->getMessage(),
                'model_used' => $this->model,
                'input_tokens' => 0,
                'output_tokens' => 0,
                'estimated_cost_usd' => 0,
                'status' => 'failed',
                'latency_ms' => $latencyMs,
            ]);

            throw $e;
        }
    }

    /**
     * Send HTTP request to Anthropic API with 60s timeout.
     */
    protected function callAnthropicApi(array $messages, string $systemPrompt, string $model, int $maxTokens): array
    {
        if (empty($this->apiKey)) {
            // If in test or sandbox without API key, return a mock response
            return $this->mockApiResponse($messages, $systemPrompt);
        }

        $response = Http::timeout(60)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post($this->apiUrl, [
                'model' => $model,
                'max_tokens' => $maxTokens,
                'system' => $systemPrompt,
                'messages' => $messages,
            ]);

        if ($response->failed()) {
            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();
            throw new Exception("Anthropic API Error ({$response->status()}): {$errorMessage}");
        }

        return $response->json();
    }

    /**
     * Build system prompt for primary school homework checking.
     */
    protected function buildHomeworkSystemPrompt(Child $child): string
    {
        return <<<PROMPT
Bạn là "Gia Sư Nhỏ" — người gia sư AI thân thiện, tận tâm, ấm áp dành cho học sinh tiểu học Việt Nam.
Bé tên là "{$child->name}", hiện đang học Lớp {$child->grade}.

NHIỆM VỤ CỦA BẠN:
1. Đọc và phân tích ảnh bài tập trong vở/sách bài tập của bé. Học sinh tiểu học viết tay bằng bút mực/bút chì, nét chữ có thể hơi run hoặc nghiêng; hãy kiên nhẫn nhận diện chữ số và từ ngữ tiếng Việt (chú ý dấu thanh sắc, huyền, hỏi, ngã, nặng, mũ, râu).
2. Kiểm tra từng câu/bài tập trong ảnh xem đúng hay sai.
3. Áp dụng phương pháp sư phạm tích cực "Sandwich Feedback":
   - Luôn khen ngợi sự nỗ lực, nét chữ hoặc tinh thần học của bé trước.
   - Nếu có lỗi sai, chỉ ra một cách ân cần, dễ hiểu, không chê bai hay làm bé tự ti.
   - Hướng dẫn từng bước cách sửa hoặc gợi ý quy tắc (ví dụ quy tắc chính tả c/k, g/gh, ngh/ng hoặc cách đặt tính cộng có nhớ).
4. ĐỊNH DẠNG ĐẦU RA BẮT BUỘC:
   Bạn CHỈ ĐƯỢC trả về DUY NHẤT một chuỗi JSON hợp lệ (không bọc trong markdown ```json``` hoặc bất kỳ văn bản nào khác ngoài JSON):
{
  "overall_feedback": "Lời nhận xét tổng thể ấm áp gửi bé (khen ngợi + động viên)",
  "items": [
    {
      "question": "Mô tả ngắn về câu hỏi/bài tập (Ví dụ: Bài 1: 24 + 18 = ?)",
      "is_correct": true,
      "explanation": "Giải thích chi tiết vì sao đúng hoặc gợi ý cách tính đúng bằng lời lẽ thân thiện"
    }
  ]
}
PROMPT;
    }

    /**
     * Build system prompt for Q&A.
     */
    protected function buildQaSystemPrompt(Child $child): string
    {
        return <<<PROMPT
Bạn là "Gia Sư Nhỏ" — gia sư AI thông thái, dễ thương và kiên nhẫn của bé {$child->name} (học sinh Lớp {$child->grade}).
Bé vừa hỏi bạn một câu hỏi thông qua giọng nói.

HÃY TRẢ LỜI BẰNG:
1. Giọng văn trong sáng, ấm áp, ngắn gọn, xưng "Gia sư" và gọi "con" hoặc "bé {$child->name}".
2. Từ ngữ đơn giản, dễ hiểu, phù hợp với lứa tuổi Lớp {$child->grade}. Dùng các ví dụ sinh động, gần gũi trong đời sống hoặc thiên nhiên.
3. Khuyến khích sự tò mò và khen ngợi câu hỏi hay của bé.
PROMPT;
    }

    /**
     * Safely parse JSON from Claude response.
     */
    protected function parseStructuredJson(string $rawContent): array
    {
        // Remove potential markdown code fences if present
        $clean = preg_replace('/^```(?:json)?\s*/i', '', trim($rawContent));
        $clean = preg_replace('/\s*```$/', '', $clean);

        $data = json_decode($clean, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data;
        }

        // Fallback: search for first { and last }
        if (preg_match('/\{[\s\S]*\}/', $rawContent, $matches)) {
            $data = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                return $data;
            }
        }

        // Return structured fallback
        return [
            'overall_feedback' => 'Gia sư đã đọc bài làm của con. ' . strip_tags($rawContent),
            'items' => [],
        ];
    }

    /**
     * Calculate estimated cost in USD based on model and token counts.
     */
    protected function calculateCost(string $model, int $inputTokens, int $outputTokens): float
    {
        if (str_contains($model, 'haiku')) {
            $inputRate = 0.80 / 1_000_000;
            $outputRate = 4.00 / 1_000_000;
        } else {
            // Sonnet pricing
            $inputRate = 3.00 / 1_000_000;
            $outputRate = 15.00 / 1_000_000;
        }

        return round(($inputTokens * $inputRate) + ($outputTokens * $outputRate), 6);
    }

    /**
     * Provide mock response when API key is not configured (e.g. initial dev/testing).
     */
    protected function mockApiResponse(array $messages, string $systemPrompt): array
    {
        $hasImage = false;
        foreach ($messages as $msg) {
            if (is_array($msg['content'])) {
                foreach ($msg['content'] as $c) {
                    if (($c['type'] ?? '') === 'image') {
                        $hasImage = true;
                    }
                }
            }
        }

        if ($hasImage) {
            $jsonMock = json_encode([
                'overall_feedback' => 'Bé viết chữ và số rất rõ ràng, thẳng hàng! Bé đã làm đúng các bài toán đặt tính.',
                'items' => [
                    [
                        'question' => 'Bài 1: Đặt tính rồi tính 28 + 35',
                        'is_correct' => true,
                        'explanation' => 'Con đặt tính thẳng cột và tính nhẩm phép cộng có nhớ rất chính xác: 8 + 5 = 13 viết 3 nhớ 1, 2 + 3 = 5 thêm 1 bằng 6.',
                    ],
                    [
                        'question' => 'Bài 2: Điền c hay k vào chỗ trống: ...on ...ua',
                        'is_correct' => true,
                        'explanation' => 'Con nhớ quy tắc chính tả rất tốt! c đi với o và u tạo thành con cua.',
                    ],
                ],
            ]);

            return [
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $jsonMock,
                    ],
                ],
                'usage' => [
                    'input_tokens' => 350,
                    'output_tokens' => 180,
                ],
            ];
        }

        return [
            'content' => [
                [
                    'type' => 'text',
                    'text' => 'Chào con! Gia sư rất vui vì con đã hỏi câu này. Câu hỏi của con thật thú vị!',
                ],
            ],
            'usage' => [
                'input_tokens' => 60,
                'output_tokens' => 80,
            ],
        ];
    }
}
