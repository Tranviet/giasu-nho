<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAuthTest extends TestCase
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

        Subject::create([
            'name' => 'Toán học',
            'code' => 'math',
        ]);
    }

    public function test_homepage_is_accessible_and_displays_product_info(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Gia Sư Nhỏ')
            ->assertSee('Đăng nhập')
            ->assertSee('Đăng ký miễn phí')
            ->assertSee('Góc học của bé');
    }

    public function test_parent_can_view_login_and_register_pages(): void
    {
        $this->get('/login')
            ->assertStatus(200)
            ->assertSee('Đăng nhập tài khoản phụ huynh');

        $this->get('/register')
            ->assertStatus(200)
            ->assertSee('Tạo tài khoản gia đình miễn phí');
    }

    public function test_parent_can_register_via_web_form_and_redirects_to_child_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'Bố Tuấn',
            'email' => 'botuan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'child_name' => 'Bé Nam',
            'child_grade' => 2,
        ]);

        $response->assertRedirect(route('child.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'botuan@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(1, $user->children()->count());
        $this->assertEquals('Bé Nam', $user->children()->first()->name);
    }

    public function test_parent_can_login_via_web_form_and_redirects_to_child_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'parentweb@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'parentweb@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('child.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_parent_can_logout_via_web_session(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
