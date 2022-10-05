<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_verification_notice_page()
    {
        $response = $this->get(route('verification.notice'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_access_verification_notice_page()
    {
        $response = $this->login()->get(route('verification.notice'));

        $response->assertStatus(200);
    }

    public function test_verification_email_is_sent_after_user_registration()
    {
        $emails = $this->app->make('mailer')->getSwiftMailer()->getTransport()->messages();
        $response = $this->login(true)->post(route('users.store'), [
            'name' => 'test_name',
            'email' => 'test@test.com',
            'password' => 'test_password',
            'password_confirmation' => 'test_password',
        ]);

        $this->assertNotEmpty($emails);
        $this->assertArrayHasKey('test@test.com', $emails->first()->getTo());
    }

    public function test_user_can_resend_verification_email()
    {
        $user = User::factory()->create();
        $emails = $this->app->make('mailer')->getSwiftMailer()->getTransport()->messages();
        $response = $this->actingAs($user)->post(route('verification.send'));

        $this->assertNotEmpty($emails);
        $this->assertArrayHasKey($user->email, $emails->first()->getTo());
    }

    public function test_guest_cannot_resend_verification_email()
    {
        $response = $this->post(route('verification.send'));

        $response->assertRedirect(route('login'));
    }

    public function test_verification_link_authorizes_user()
    {
        $response = $this->login(true)->post(route('users.store'), [
            'name' => 'test_name',
            'email' => 'test@test.com',
            'password' => 'test_password',
            'password_confirmation' => 'test_password',
        ]);

        $user = User::where('email', 'test@test.com')->first();
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(10),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $response = $this->actingAs($user)->get($url);

        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_verification_link_does_not_authorize_incorrect_users()
    {
        $response = $this->login(true)->post(route('users.store'), [
            'name' => 'test_name',
            'email' => 'test@test.com',
            'password' => 'test_password',
            'password_confirmation' => 'test_password',
        ]);

        $user = User::where('email', 'test@test.com')->first();
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(10),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $response = $this->login()->get($url);

        $response->assertStatus(403);
    }

    public function test_verification_link_does_not_authorize_guests()
    {
        $response = $this->login(true)->post(route('users.store'), [
            'name' => 'test_name',
            'email' => 'test@test.com',
            'password' => 'test_password',
            'password_confirmation' => 'test_password',
        ]);

        $user = User::where('email', 'test@test.com')->first();
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(10),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $response = $this->get($url);

        $response->assertStatus(403);
    }

    /*
        Helpers
    */
    private function login($asAdmin = false)
	{
		$user = $asAdmin
			? User::factory()->admin()->create()
			: User::factory()->create();
		return $this->actingAs($user);
	}
}
