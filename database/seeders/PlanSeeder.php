<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Miễn phí',
                'code' => 'free',
                'price' => 0,
                'monthly_ai_quota' => 10,
                'description' => 'Trải nghiệm miễn phí 10 lượt hỏi đáp và chấm bài AI mỗi tháng.',
            ],
            [
                'name' => 'Gia Đình Cơ Bản',
                'code' => 'basic',
                'price' => 99000,
                'monthly_ai_quota' => 100,
                'description' => 'Gói phổ biến: 100 lượt chấm bài và hỏi đáp AI mỗi tháng cho cả gia đình.',
            ],
            [
                'name' => 'Gia Đình Nâng Cao',
                'code' => 'premium',
                'price' => 199000,
                'monthly_ai_quota' => 300,
                'description' => 'Học tập không giới hạn: 300 lượt AI mỗi tháng kèm ưu tiên phản hồi nhanh.',
            ],
        ];

        foreach ($plans as $p) {
            Plan::updateOrCreate(['code' => $p['code']], $p);
        }
    }
}
