<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Plan::create([
            'name' => 'Miễn phí',
            'code' => 'free',
            'price' => 0,
            'monthly_ai_quota' => 10,
        ]);
    }

    public function test_parent_can_register_and_receives_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Nguyen Van Phụ Huynh',
            'email' => 'parent@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'parent@example.com',
        ]);

        // Verify free subscription was created
        $user = User::where('email', 'parent@example.com')->first();
        $this->assertNotNull($user->activeSubscription);
    }

    public function test_registration_validation_fails_on_duplicate_email(): void
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_parent_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'token',
            ]);
    }

    public function test_parent_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'login@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_parent_can_view_profile_and_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $meResponse = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'children',
                'subscription',
            ]);

        $logoutResponse = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200);
    }
}
