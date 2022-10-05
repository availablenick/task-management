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
        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
    }

    public function test_user_details_page_can_be_rendered()
    {
        $user = User::factory()->create();
        $response = $this->get(route('users.show', $user));

        $response->assertStatus(200);
    }

    public function test_user_cannot_be_created_without_name()
    {
        $response = $this->loginAsAdmin()->post(route('users.store'));

        $response->assertInvalid(['name']);
    }

    public function test_user_cannot_be_created_without_email()
    {
        $response = $this->loginAsAdmin()->post(route('users.store'));

        $response->assertInvalid(['email']);
    }

    public function test_user_cannot_be_created_with_invalid_email()
    {
        $response = $this->loginAsAdmin()->post(route('users.store'), [
            'email' => 'test'
        ]);

        $response->assertInvalid(['email']);
    }

    public function test_user_cannot_be_created_without_password()
    {
        $response = $this->loginAsAdmin()->post(route('users.store'));

        $response->assertInvalid(['password']);
    }

    public function test_user_cannot_be_created_with_invalid_password_length()
    {
        $response = $this->loginAsAdmin()->post(route('users.store'), [
            'password' => '1234567',
        ]);

        $response->assertInvalid(['password']);
    }

    public function test_user_cannot_be_created_without_password_confirmation()
    {
        $response = $this->loginAsAdmin()->post(route('users.store'), [
            'password' => 'test_password',
        ]);

        $response->assertInvalid(['password']);
    }

    public function test_user_cannot_be_created_with_wrong_password_confirmation()
    {
        $response = $this->loginAsAdmin()->post(route('users.store'), [
            'password' => 'test_password',
            'password_confirmation' => 'test',
        ]);

        $response->assertInvalid(['password']);
    }

    public function test_user_cannot_be_updated_with_invalid_email()
    {
        $user = User::factory()->create();
        $response = $this->loginAsAdmin()->put(route('users.update', $user), [
            'email' => 'test_email',
        ]);

        $response->assertInvalid(['email']);
    }

    public function test_guest_cannot_access_creation_page()
    {
        $response = $this->get(route('users.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_edit_page()
    {
        $user = User::factory()->create();
        $response = $this->get(route('users.edit', $user));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_create_users()
    {
        $response = $this->post(route('users.store'), [
            'name' => 'test_name',
            'email' => 'test@test.com',
            'password' => 'test_password',
        ]);

        $this->assertDatabaseMissing('users', [
            'name' => 'test_name',
            'email' => 'test@test.com',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_edit_users()
    {
        $user = User::factory()->create();
        $response = $this->put(route('users.update', $user), [
            'name' => 'edit_' . $user->name,
            'email' => 'edit_' . $user->email,
            'password' => 'edit_password',
        ]);

        $this->assertDatabaseMissing('users', [
            'name' => 'edit_' . $user->name,
            'email' => 'edit_' . $user->email,
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_delete_users()
    {
        $user = User::factory()->create();
        $response = $this->delete(route('users.destroy', $user));

        $this->assertModelExists($user);
        $this->assertNotSoftDeleted($user);
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_user_cannot_access_creation_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('users.create'));

        $response->assertRedirect(route('unauthorized'));
    }

    public function test_non_admin_user_cannot_access_another_user_edit_page()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $response = $this->actingAs($user2)->get(route('users.edit', $user1));

        $response->assertRedirect(route('unauthorized'));
    }

    public function test_non_admin_user_can_access_their_edit_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('users.edit', $user));

        $response->assertStatus(200);
    }

    public function test_non_admin_user_cannot_create_users()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('users.store'), [
            'name' => 'test_name',
            'email' => 'test@test.com',
            'password' => 'test_password',
        ]);

        $this->assertDatabaseMissing('users', [
            'name' => 'test_name',
            'email' => 'test@test.com',
        ]);

        $response->assertRedirect(route('unauthorized'));
    }

    public function test_non_admin_user_cannot_edit_another_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $response = $this->actingAs($user2)->put(route('users.update', $user1), [
            'name' => 'edit_' . $user1->name,
            'email' => 'edit_' . $user1->email,
            'password' => 'edit_password',
        ]);

        $this->assertDatabaseMissing('users', [
            'name' => 'edit_' . $user1->name,
            'email' => 'edit_' . $user1->email,
        ]);

        $response->assertRedirect(route('unauthorized'));
    }

    public function test_non_admin_user_can_edit_themself()
    {
        $user = User::factory()->create();
        $newData = [
            'email' => 'edit_' . $user->email,
            'name' => 'edit_' . $user->name,
        ];

        $response = $this->actingAs($user)->put(route('users.update', $user), $newData);

        $this->assertDatabaseHas('users', $newData);
        $response->assertRedirect(route('users.show', $user));
    }

    public function test_non_admin_user_cannot_delete_another_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $response = $this->actingAs($user2)->delete(route('users.destroy', $user1));

        $this->assertModelExists($user1);
        $this->assertNotSoftDeleted($user1);
        $response->assertRedirect(route('unauthorized'));
    }

    public function test_non_admin_user_can_delete_themself()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete(route('users.destroy', $user));

        $this->assertModelExists($user);
        $this->assertSoftDeleted($user);
        $response->assertRedirect(route('users.index'));
    }

    public function test_admin_can_access_creation_page()
    {
        $response = $this->loginAsAdmin()->get(route('users.create'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_edit_page()
    {
        $user = User::factory()->create();
        $response = $this->loginAsAdmin()->get(route('users.edit', $user));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_users()
    {
        Storage::fake();
        $response = $this->loginAsAdmin()->post(route('users.store'), [
            'name' => 'test_name',
            'email' => 'test@test.com',
            'password' => 'test_password',
            'password_confirmation' => 'test_password',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'test_name',
            'email' => 'test@test.com',
        ]);

        $user = User::where('email', 'test@test.com')->first();
        $this->assertNotNull($user->avatar_path);
        Storage::disk()->assertExists($user->avatar_path);
        $response->assertRedirect(route('users.show', $user));
    }

    public function test_admin_can_edit_users()
    {
        Storage::fake();
        $response = $this->loginAsAdmin()->post(route('users.store'), [
            'name' => 'test_name',
            'email' => 'test@test.com',
            'password' => 'test_password',
            'password_confirmation' => 'test_password',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $user = User::where('email', 'test@test.com')->first();
        $oldFilepath = $user->avatar_path;
        $response = $this->loginAsAdmin()->put(route('users.update', $user), [
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
        $response->assertRedirect(route('users.show', $user));
    }

    public function test_admin_can_delete_users()
    {
        Storage::fake();
        $response = $this->loginAsAdmin()->post(route('users.store'), [
            'name' => 'test_name',
            'email' => 'test@test.com',
            'password' => 'test_password',
            'password_confirmation' => 'test_password',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $user = User::where('email', 'test@test.com')->first();
        $response = $this->loginAsAdmin()->delete(route('users.destroy', $user));

        $this->assertModelExists($user);
        $this->assertSoftDeleted($user);
        Storage::disk()->assertMissing($user->avatar_path);
        $response->assertRedirect(route('users.index'));
    }

    public function test_unverified_non_admin_user_cannot_access_edit_page()
    {
        $user = User::factory()->unverified()->create();
        $response = $this->actingAs($user)->get(route('users.edit', $user));

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_non_admin_user_cannot_edit_themself()
    {
        $user = User::factory()->unverified()->create();
        $response = $this->actingAs($user)->put(route('users.update', $user), [
            'name' => 'edit_' . $user->name,
            'email' => 'edit_' . $user->email,
        ]);

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_non_admin_user_cannot_delete_themself()
    {
        $user = User::factory()->unverified()->create();
        $response = $this->actingAs($user)->delete(route('users.destroy', $user));

        $response->assertRedirect(route('verification.notice'));
    }

    /*
        Helpers
    */
    private function loginAsAdmin()
	{
		return $this->actingAs(User::factory()->admin()->create());
	}
}
