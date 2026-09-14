<?php

namespace Tests\Feature;

use App\Models\AiInteraction;
use App\Models\Child;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AiTutoringTest extends TestCase
{
    use RefreshDatabase;

    protected User $parent;
    protected Child $child;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $freePlan = Plan::create([
            'name' => 'Miễn phí',
            'code' => 'free',
            'price' => 0,
            'monthly_ai_quota' => 3, // Set low quota for testing
        ]);

        $this->parent = User::factory()->create();

        Subscription::create([
            'user_id' => $this->parent->id,
            'plan_id' => $freePlan->id,
            'status' => 'active',
            'current_period_start' => now()->startOfMonth(),
            'current_period_end' => now()->endOfMonth(),
        ]);

        $this->child = Child::create([
            'user_id' => $this->parent->id,
            'name' => 'Bé An',
            'grade' => 2,
        ]);
    }

    public function test_child_can_ask_ai_question(): void
    {
        $response = $this->actingAs($this->parent)
            ->postJson('/api/v1/ai/qa', [
                'child_id' => $this->child->id,
                'question' => 'Vì sao bầu trời lại màu xanh hả gia sư?',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'answer',
                'quota_remaining',
            ]);

        $this->assertDatabaseHas('ai_interactions', [
            'user_id' => $this->parent->id,
            'child_id' => $this->child->id,
            'type' => 'qa_chat',
            'status' => 'success',
        ]);
    }

    public function test_child_can_upload_homework_photo_for_ai_grading(): void
    {
        $image = UploadedFile::fake()->image('homework.jpg', 800, 600);

        $response = $this->actingAs($this->parent)
            ->postJson('/api/v1/ai/homework-check', [
                'child_id' => $this->child->id,
                'image' => $image,
                'note' => 'Bài toán cộng có nhớ',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'submission_id',
                'overall_feedback',
                'items',
                'image_url',
                'quota_remaining',
            ]);

        $this->assertDatabaseHas('exercise_submissions', [
            'child_id' => $this->child->id,
            'exercise_id' => null,
        ]);

        $this->assertDatabaseHas('ai_interactions', [
            'user_id' => $this->parent->id,
            'child_id' => $this->child->id,
            'type' => 'homework_check',
            'status' => 'success',
        ]);
    }

    public function test_ai_calls_are_blocked_when_monthly_quota_is_exhausted(): void
    {
        // Consume all 3 quota calls
        for ($i = 0; $i < 3; $i++) {
            AiInteraction::create([
                'user_id' => $this->parent->id,
                'child_id' => $this->child->id,
                'type' => 'qa_chat',
                'input_summary' => "Test $i",
                'ai_response' => 'Answer',
                'model_used' => 'test-model',
                'status' => 'success',
                'created_at' => now(),
            ]);
        }

        // The 4th call must be blocked with HTTP 402
        $response = $this->actingAs($this->parent)
            ->postJson('/api/v1/ai/qa', [
                'child_id' => $this->child->id,
                'question' => 'Câu hỏi khi đã hết quota',
            ]);

        $response->assertStatus(402)
            ->assertJsonStructure([
                'message',
                'quota',
            ]);

        // Verify no new AI interaction was saved
        $this->assertEquals(3, AiInteraction::where('user_id', $this->parent->id)->count());
    }

    public function test_child_progress_endpoint_returns_stats(): void
    {
        $response = $this->actingAs($this->parent)
            ->getJson("/api/v1/children/{$this->child->id}/progress");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'child' => ['id', 'name', 'grade'],
                'stats' => ['total_submissions', 'correct_submissions', 'accuracy_rate'],
                'topic_masteries',
                'recent_submissions',
            ]);
    }
}
