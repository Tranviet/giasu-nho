<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Toán học',
                'code' => 'math',
                'icon' => 'calculator',
                'description' => 'Môn Toán tiểu học theo chương trình GDPT 2018: số học, hình học cơ bản, giải toán có lời văn.',
            ],
            [
                'name' => 'Tiếng Việt',
                'code' => 'vietnamese',
                'icon' => 'book-open',
                'description' => 'Môn Tiếng Việt tiểu học: quy tắc chính tả, từ ngữ, luyện từ và câu, đọc hiểu.',
            ],
        ];

        foreach ($subjects as $s) {
            Subject::updateOrCreate(['code' => $s['code']], $s);
        }
    }
}
