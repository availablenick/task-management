<?php

namespace Tests\Feature;

Use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }

    public function test_guest_cannot_login_without_email_or_password()
    {
        $response = $this->post(route('authenticate'));

        $response->assertInvalid(['email', 'password']);
    }

    public function test_guest_can_login()
    {
        $user = User::factory()->create();
        $response = $this->post(route('authenticate'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_user_cannot_access_login_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_authenticated_user_cannot_login()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('authenticate'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
    }

    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
    }
}
