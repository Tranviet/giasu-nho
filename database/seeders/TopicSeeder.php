<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $math = Subject::where('code', 'math')->first();
        $vietnamese = Subject::where('code', 'vietnamese')->first();

        $topics = [
            // Math Grade 1
            [
                'subject_id' => $math->id,
                'grade' => 1,
                'name' => 'Các số trong phạm vi 10',
                'slug' => 'cac-so-trong-pham-vi-10',
                'description' => 'Đếm, so sánh và nhận biết thứ tự các số từ 0 đến 10.',
                'sort_order' => 1,
            ],
            [
                'subject_id' => $math->id,
                'grade' => 1,
                'name' => 'Phép cộng và trừ trong phạm vi 10',
                'slug' => 'phep-cong-tru-pham-vi-10',
                'description' => 'Thực hành tính nhẩm cộng và trừ không nhớ trong phạm vi 10.',
                'sort_order' => 2,
            ],
            // Math Grade 2
            [
                'subject_id' => $math->id,
                'grade' => 2,
                'name' => 'Phép cộng có nhớ trong phạm vi 100',
                'slug' => 'phep-cong-co-nho-pham-vi-100',
                'description' => 'Kỹ năng đặt tính rồi tính và tính nhẩm phép cộng 2 chữ số có nhớ.',
                'sort_order' => 1,
            ],
            [
                'subject_id' => $math->id,
                'grade' => 2,
                'name' => 'Phép trừ có nhớ trong phạm vi 100',
                'slug' => 'phep-tru-co-nho-pham-vi-100',
                'description' => 'Kỹ năng đặt tính rồi tính phép trừ có nhớ trong phạm vi 100.',
                'sort_order' => 2,
            ],
            [
                'subject_id' => $math->id,
                'grade' => 2,
                'name' => 'Bảng nhân 2 và nhân 5',
                'slug' => 'bang-nhan-2-va-5',
                'description' => 'Học thuộc và vận dụng bảng nhân 2, bảng nhân 5 vào bài toán thực tế.',
                'sort_order' => 3,
            ],

            // Vietnamese Grade 1
            [
                'subject_id' => $vietnamese->id,
                'grade' => 1,
                'name' => 'Quy tắc chính tả c/k, g/gh, ngh/ng',
                'slug' => 'quy-tac-chinh-ta-c-k-g-gh',
                'description' => 'Quy tắc điền phụ âm khi đứng trước các nguyên âm e, ê, i.',
                'sort_order' => 1,
            ],
            [
                'subject_id' => $vietnamese->id,
                'grade' => 1,
                'name' => 'Phân biệt dấu hỏi và dấu ngã',
                'slug' => 'phan-biet-dau-hoi-va-nga',
                'description' => 'Luyện tập phát âm và viết đúng thanh hỏi và thanh ngã trong các từ ngữ quen thuộc.',
                'sort_order' => 2,
            ],
            // Vietnamese Grade 2
            [
                'subject_id' => $vietnamese->id,
                'grade' => 2,
                'name' => 'Từ chỉ sự vật, hoạt động, đặc điểm',
                'slug' => 'tu-chi-su-vat-hoat-dong-dac-diem',
                'description' => 'Nhận biết danh từ, động từ, tính từ cơ bản qua các câu chuyện ngắn.',
                'sort_order' => 1,
            ],
            [
                'subject_id' => $vietnamese->id,
                'grade' => 2,
                'name' => 'Dấu chấm và Dấu chấm hỏi',
                'slug' => 'dau-cham-va-dau-cham-hoi',
                'description' => 'Cách dùng dấu câu thích hợp khi kết thúc câu kể và câu hỏi.',
                'sort_order' => 2,
            ],
        ];

        foreach ($topics as $t) {
            Topic::updateOrCreate(
                ['subject_id' => $t['subject_id'], 'slug' => $t['slug']],
                $t
            );
        }
    }
}
