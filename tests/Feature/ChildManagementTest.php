<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChildManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_create_child_profile(): void
    {
        $parent = User::factory()->create();

        $response = $this->actingAs($parent)
            ->postJson('/api/v1/children', [
                'name' => 'Bé Minh',
                'grade' => 2,
                'avatar' => 'tiger',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('child.name', 'Bé Minh')
            ->assertJsonPath('child.grade', 2);

        $this->assertDatabaseHas('children', [
            'user_id' => $parent->id,
            'name' => 'Bé Minh',
            'grade' => 2,
        ]);
    }

    public function test_parent_can_view_only_their_own_children(): void
    {
        $parentA = User::factory()->create();
        $parentB = User::factory()->create();

        $childA = Child::create([
            'user_id' => $parentA->id,
            'name' => 'Con của A',
            'grade' => 1,
        ]);

        $childB = Child::create([
            'user_id' => $parentB->id,
            'name' => 'Con của B',
            'grade' => 3,
        ]);

        // Parent A lists children
        $response = $this->actingAs($parentA)->getJson('/api/v1/children');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $childA->id);

        // Parent A views child A (Allowed)
        $this->actingAs($parentA)
            ->getJson("/api/v1/children/{$childA->id}")
            ->assertStatus(200);

        // Parent A attempts to view child B (Forbidden 403)
        $this->actingAs($parentA)
            ->getJson("/api/v1/children/{$childB->id}")
            ->assertStatus(403);

        // Parent A attempts to update child B (Forbidden 403)
        $this->actingAs($parentA)
            ->putJson("/api/v1/children/{$childB->id}", [
                'name' => 'Hacker Name',
                'grade' => 4,
            ])
            ->assertStatus(403);

        // Parent A attempts to delete child B (Forbidden 403)
        $this->actingAs($parentA)
            ->deleteJson("/api/v1/children/{$childB->id}")
            ->assertStatus(403);
    }
}
