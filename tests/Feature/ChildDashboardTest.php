<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\Plan;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChildDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $math = Subject::create([
            'name' => 'Toán học',
            'code' => 'math',
        ]);

        Topic::create([
            'subject_id' => $math->id,
            'grade' => 2,
            'name' => 'Phép cộng có nhớ trong phạm vi 100',
            'slug' => 'phep-cong-co-nho-pham-vi-100',
        ]);
    }

    public function test_guest_can_access_child_dashboard_with_demo_profile(): void
    {
        $response = $this->get('/child-dashboard');

        $response->assertStatus(200)
            ->assertSee('Gia Sư Nhỏ')
            ->assertSee('Bé Minh')
            ->assertSee('Toán học')
            ->assertSee('Hỏi bằng giọng nói')
            ->assertSee('Chụp ảnh bài tập');
    }

    public function test_authenticated_parent_sees_their_own_child_on_dashboard(): void
    {
        $parent = User::factory()->create(['name' => 'Mẹ Hoa']);
        $child = Child::create([
            'user_id' => $parent->id,
            'name' => 'Bé Thảo',
            'grade' => 2,
            'avatar' => 'cat',
        ]);

        $response = $this->actingAs($parent)->get('/child-dashboard');

        $response->assertStatus(200)
            ->assertSee('Bé Thảo')
            ->assertSee('Lớp 2');
    }
}
