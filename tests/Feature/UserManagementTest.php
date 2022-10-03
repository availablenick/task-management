<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        $this->assertNotSoftDeleted($user);
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
        $this->assertNotSoftDeleted($user1);
        $response->assertRedirect('/unauthorized');
    }

    public function test_non_admin_user_can_delete_themself()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('/users/' . $user->id);

        $this->assertModelExists($user);
        $this->assertSoftDeleted($user);
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
        Storage::fake();
        $response = $this->loginAsAdmin()->post('/users', [
            'name' => 'test_name',
            'email' => 'test_email',
            'password' => 'test_password',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'test_name',
            'email' => 'test_email',
        ]);

        $user = User::where('email', 'test_email')->first();
        $this->assertNotNull($user->avatar_path);
        Storage::disk()->assertExists($user->avatar_path);
        $response->assertRedirect('/users/' . $user->id);
    }

    public function test_admin_can_edit_users()
    {
        Storage::fake();
        $response = $this->loginAsAdmin()->post('/users', [
            'name' => 'test_name',
            'email' => 'test_email',
            'password' => 'test_password',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $user = User::where('email', 'test_email')->first();
        $oldFilepath = $user->avatar_path;
        $response = $this->loginAsAdmin()->put('/users/' . $user->id, [
            'email' => 'edit_' . $user->email,
            'name' => 'edit_' . $user->name,
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'edit_' . $user->email,
            'name' => 'edit_' . $user->name,
        ]);

        $user->refresh();
        $this->assertNotNull($user->avatar_path);
        Storage::disk()->assertExists($user->avatar_path);
        Storage::disk()->assertMissing($oldFilepath);
        $response->assertRedirect('/users/' . $user->id);
    }

    public function test_admin_can_delete_users()
    {
        Storage::fake();
        $response = $this->loginAsAdmin()->post('/users', [
            'name' => 'test_name',
            'email' => 'test_email',
            'password' => 'test_password',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $user = User::where('email', 'test_email')->first();
        $response = $this->loginAsAdmin()->delete('/users/' . $user->id);

        $this->assertModelExists($user);
        $this->assertSoftDeleted($user);
        Storage::disk()->assertMissing($user->avatar_path);
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
