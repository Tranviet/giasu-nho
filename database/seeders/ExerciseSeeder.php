<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Math Grade 2: Phép cộng có nhớ trong phạm vi 100
        $additionTopic = Topic::where('slug', 'phep-cong-co-nho-pham-vi-100')->first();
        if ($additionTopic) {
            $additionExercises = [
                [
                    'topic_id' => $additionTopic->id,
                    'type' => 'multiple_choice',
                    'difficulty' => 1,
                    'content' => [
                        'question' => 'Kết quả của phép tính 27 + 35 là bao nhiêu?',
                        'options' => [
                            'A' => '52',
                            'B' => '62',
                            'C' => '72',
                            'D' => '61',
                        ],
                        'answer' => 'B',
                        'explanation' => 'Đặt tính: 7 + 5 = 12, viết 2 nhớ 1. 2 + 3 = 5, thêm 1 bằng 6. Vậy 27 + 35 = 62.',
                    ],
                ],
                [
                    'topic_id' => $additionTopic->id,
                    'type' => 'fill_blank',
                    'difficulty' => 2,
                    'content' => [
                        'question' => 'Điền số thích hợp vào chỗ trống: 48 + 16 = ...',
                        'answer' => '64',
                        'explanation' => '8 + 6 = 14, viết 4 nhớ 1. 4 + 1 = 5, thêm 1 bằng 6. Kết quả là 64.',
                    ],
                ],
                [
                    'topic_id' => $additionTopic->id,
                    'type' => 'multiple_choice',
                    'difficulty' => 2,
                    'content' => [
                        'question' => 'Một đàn gà có 39 con gà mái và 17 con gà trống. Hỏi đàn gà có tất cả bao nhiêu con?',
                        'options' => [
                            'A' => '56',
                            'B' => '46',
                            'C' => '55',
                            'D' => '57',
                        ],
                        'answer' => 'A',
                        'explanation' => 'Tổng số con gà là: 39 + 17 = 56 con gà.',
                    ],
                ],
            ];

            foreach ($additionExercises as $ex) {
                Exercise::create($ex);
            }
        }

        // 2. Vietnamese Grade 1: Quy tắc chính tả c/k
        $spellingTopic = Topic::where('slug', 'quy-tac-chinh-ta-c-k-g-gh')->first();
        if ($spellingTopic) {
            $spellingExercises = [
                [
                    'topic_id' => $spellingTopic->id,
                    'type' => 'multiple_choice',
                    'difficulty' => 1,
                    'content' => [
                        'question' => 'Từ nào sau đây viết ĐÚNG chính tả?',
                        'options' => [
                            'A' => 'con cá',
                            'B' => 'kon cá',
                            'C' => 'cây kọ',
                            'D' => 'kua đồng',
                        ],
                        'answer' => 'A',
                        'explanation' => 'Quy tắc: Chữ "k" chỉ đứng trước các nguyên âm e, ê, i (ví dụ: con kiến, cây kéo). Với các âm o, u, a,... ta viết chữ "c". Vậy "con cá" là đúng.',
                    ],
                ],
                [
                    'topic_id' => $spellingTopic->id,
                    'type' => 'fill_blank',
                    'difficulty' => 1,
                    'content' => [
                        'question' => 'Điền chữ "c" hoặc "k" vào chỗ trống để hoàn thành từ: cái ...éo',
                        'answer' => 'k',
                        'explanation' => 'Đứng trước âm "e" ta phải dùng chữ "k", tạo thành "cái kéo".',
                    ],
                ],
            ];

            foreach ($spellingExercises as $ex) {
                Exercise::create($ex);
            }
        }
    }
}
