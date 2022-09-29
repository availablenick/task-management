<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_page_can_be_rendered()
    {
        $response = $this->get('/users');

        $response->assertStatus(200);
    }

    public function test_user_details_page_can_be_rendered()
    {
        $user = User::factory()->create();
        $response = $this->get('/users/' . $user->id);

        $response->assertStatus(200);
    }

    public function test_logged_out_user_cannot_create_users()
    {
        $response = $this->post('/users', [
            'name' => 'test_name',
            'email' => 'test_email',
            'password' => 'test_password',
        ]);

        $this->assertDatabaseMissing('users', [
            'name' => 'test_name',
            'email' => 'test_email',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_logged_out_user_cannot_edit_users()
    {
        $user = User::factory()->create();
        $response = $this->put('/users/' . $user->id, [
            'name' => 'edit_' . $user->name,
            'email' => 'edit_' . $user->email,
            'password' => 'edit_password',
        ]);

        $this->assertDatabaseMissing('users', [
            'name' => 'edit_' . $user->name,
            'email' => 'edit_' . $user->email,
        ]);

        $response->assertRedirect('/login');
    }

    public function test_logged_out_user_cannot_delete_users()
    {
        $user = User::factory()->create();
        $response = $this->delete('/users/' . $user->id);

        $this->assertModelExists($user);
        $response->assertRedirect('/login');
    }

    public function test_user_can_be_created()
    {
        $response = $this->login()->post('/users', [
            'name' => 'test_name',
            'email' => 'test_email',
            'password' => 'test_password',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'test_name',
            'email' => 'test_email',
        ]);

        $user = User::where('email', 'test_email')->first();
        $response->assertRedirect('/users/' . $user->id);
    }
    
    public function test_user_can_be_updated()
    {
        $user = User::factory()->create();
        $newData = [
            'email' => 'edit_' . $user->email,
            'name' => 'edit_' . $user->name,
        ];

        $response = $this->login()->put('/users/' . $user->id, $newData);

        $this->assertDatabaseHas('users', $newData);
        $response->assertRedirect('/users/' . $user->id);
    }

    public function test_user_can_be_deleted()
    {
        $user = User::factory()->create();
        $response = $this->login()->delete('/users/' . $user->id);

        $this->assertModelMissing($user);
        $response->assertRedirect('/users');
    }

    /*
        Helpers
    */
    private function login()
	{
		$user = User::factory()->create();
		return $this->actingAs($user);
	}
}
