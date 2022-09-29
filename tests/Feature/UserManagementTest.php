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

    public function test_guest_cannot_access_creation_page()
    {
        $response = $this->get('/users/create');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_edit_page()
    {
        $user = User::factory()->create();
        $response = $this->get('/users/' . $user->id . '/edit');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_create_users()
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

    public function test_guest_cannot_edit_users()
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

    public function test_guest_cannot_delete_users()
    {
        $user = User::factory()->create();
        $response = $this->delete('/users/' . $user->id);

        $this->assertModelExists($user);
        $response->assertRedirect('/login');
    }

    public function test_non_admin_user_cannot_access_creation_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/users/create');

        $response->assertRedirect('/unauthorized');
    }

    public function test_non_admin_user_cannot_access_another_user_edit_page()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $response = $this->actingAs($user2)->get('/users/' . $user1->id . '/edit');

        $response->assertRedirect('/unauthorized');
    }

    public function test_non_admin_user_can_access_their_edit_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/users/' . $user->id . '/edit');

        $response->assertStatus(200);
    }

    public function test_non_admin_user_cannot_create_users()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/users', [
            'name' => 'test_name',
            'email' => 'test_email',
            'password' => 'test_password',
        ]);

        $this->assertDatabaseMissing('users', [
            'name' => 'test_name',
            'email' => 'test_email',
        ]);

        $response->assertRedirect('/unauthorized');
    }

    public function test_non_admin_user_cannot_edit_another_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $response = $this->actingAs($user2)->put('/users/' . $user1->id, [
            'name' => 'edit_' . $user1->name,
            'email' => 'edit_' . $user1->email,
            'password' => 'edit_password',
        ]);

        $this->assertDatabaseMissing('users', [
            'name' => 'edit_' . $user1->name,
            'email' => 'edit_' . $user1->email,
        ]);

        $response->assertRedirect('/unauthorized');
    }

    public function test_non_admin_user_can_edit_themself()
    {
        $user = User::factory()->create();
        $newData = [
            'email' => 'edit_' . $user->email,
            'name' => 'edit_' . $user->name,
        ];

        $response = $this->actingAs($user)->put('/users/' . $user->id, $newData);

        $this->assertDatabaseHas('users', $newData);
        $response->assertRedirect('/users/' . $user->id);
    }

    public function test_non_admin_user_cannot_delete_another_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $response = $this->actingAs($user2)->delete('/users/' . $user1->id);

        $this->assertModelExists($user1);
        $response->assertRedirect('/unauthorized');
    }

    public function test_non_admin_user_can_delete_themself()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('/users/' . $user->id);

        $this->assertModelMissing($user);
        $response->assertRedirect('/users');
    }

    public function test_admin_can_access_creation_page()
    {
        $response = $this->loginAsAdmin()->get('/users/create');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_edit_page()
    {
        $user = User::factory()->create();
        $response = $this->loginAsAdmin()->get('/users/' . $user->id . '/edit');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_users()
    {
        $response = $this->loginAsAdmin()->post('/users', [
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

    public function test_admin_can_edit_users()
    {
        $user = User::factory()->create();
        $newData = [
            'email' => 'edit_' . $user->email,
            'name' => 'edit_' . $user->name,
        ];

        $response = $this->loginAsAdmin()->put('/users/' . $user->id, $newData);

        $this->assertDatabaseHas('users', $newData);
        $response->assertRedirect('/users/' . $user->id);
    }

    public function test_admin_can_delete_users()
    {
        $user = User::factory()->create();
        $response = $this->loginAsAdmin()->delete('/users/' . $user->id);

        $this->assertModelMissing($user);
        $response->assertRedirect('/users');
    }

    /*
        Helpers
    */
    private function loginAsAdmin()
	{
		return $this->actingAs(User::factory()->admin()->create());
	}
}
