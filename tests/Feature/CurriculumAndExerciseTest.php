<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\Exercise;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumAndExerciseTest extends TestCase
{
    use RefreshDatabase;

    protected Subject $math;
    protected Topic $topic;
    protected Exercise $exercise;

    protected function setUp(): void
    {
        parent::setUp();

        $this->math = Subject::create([
            'name' => 'Toán học',
            'code' => 'math',
        ]);

        $this->topic = Topic::create([
            'subject_id' => $this->math->id,
            'grade' => 2,
            'name' => 'Phép cộng có nhớ',
            'slug' => 'phep-cong-co-nho',
        ]);

        $this->exercise = Exercise::create([
            'topic_id' => $this->topic->id,
            'type' => 'multiple_choice',
            'difficulty' => 1,
            'content' => [
                'question' => '15 + 8 = ?',
                'options' => ['A' => '22', 'B' => '23', 'C' => '24'],
                'answer' => 'B',
                'explanation' => '15 + 8 = 23',
            ],
        ]);
    }

    public function test_public_can_browse_subjects_and_topics(): void
    {
        $response = $this->getJson('/api/v1/subjects');
        $response->assertStatus(200)
            ->assertJsonPath('data.0.code', 'math');

        $topicResponse = $this->getJson('/api/v1/topics?grade=2');
        $topicResponse->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_student_can_submit_exercise_and_receive_instant_grading(): void
    {
        $parent = User::factory()->create();
        $child = Child::create([
            'user_id' => $parent->id,
            'name' => 'Bé Lan',
            'grade' => 2,
        ]);

        // Submit correct answer 'B'
        $response = $this->actingAs($parent)
            ->postJson("/api/v1/exercises/{$this->exercise->id}/submit", [
                'child_id' => $child->id,
                'answer' => 'B',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('is_correct', true)
            ->assertJsonPath('expected_answer', 'B')
            ->assertJsonPath('topic_mastery_score', 100);

        $this->assertDatabaseHas('exercise_submissions', [
            'child_id' => $child->id,
            'exercise_id' => $this->exercise->id,
            'is_correct' => true,
        ]);

        $this->assertDatabaseHas('child_topic_mastery', [
            'child_id' => $child->id,
            'topic_id' => $this->topic->id,
            'mastery_score' => 100,
        ]);
    }

    public function test_user_cannot_submit_exercise_for_another_users_child(): void
    {
        $parentA = User::factory()->create();
        $parentB = User::factory()->create();

        $childB = Child::create([
            'user_id' => $parentB->id,
            'name' => 'Bé B',
            'grade' => 2,
        ]);

        // Parent A attempts to submit for Child B
        $response = $this->actingAs($parentA)
            ->postJson("/api/v1/exercises/{$this->exercise->id}/submit", [
                'child_id' => $childB->id,
                'answer' => 'B',
            ]);

        $response->assertStatus(403);
    }
}
