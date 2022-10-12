<?php

namespace Tests\Feature;

use App\Models\AssignmentAlert;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_list_page_can_be_rendered()
    {
        $response = $this->login()->get(route('projects.index'));

        $response->assertStatus(200);
    }

    public function test_project_creation_page_can_be_rendered()
    {
        $response = $this->login(true)->get(route('projects.create'));

        $response->assertStatus(200);
    }

    public function test_project_details_page_can_be_rendered()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->login()->get(route('projects.show', $project));

        $response->assertStatus(200);
    }

    public function test_project_edit_page_can_be_rendered()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->login(true)->get(route('projects.edit', $project));

        $response->assertStatus(200);
    }

    public function test_project_cannot_be_created_without_title()
    {
        $response = $this->login(true)->post(route('projects.store'));

        $response->assertInvalid(['title']);
    }

    public function test_project_cannot_be_created_without_deadline()
    {
        $response = $this->login(true)->post(route('projects.store'));

        $response->assertInvalid(['deadline']);
    }

    public function test_project_cannot_be_created_with_misformatted_deadline()
    {
        $response = $this->login(true)->post(route('projects.store'), [
            'deadline' => '2001 01 01',
        ]);

        $response->assertInvalid(['deadline']);
    }

    public function test_project_cannot_be_created_with_past_deadline()
    {
        $response = $this->login(true)->post(route('projects.store'), [
            'deadline' => '2001-01-01',
        ]);

        $response->assertInvalid(['deadline']);
    }

    public function test_project_cannot_be_created_with_invalid_status_number()
    {
        $response = $this->login(true)->post(route('projects.store'), [
            'status' => '2',
        ]);

        $response->assertInvalid(['status']);
    }

    public function test_project_cannot_be_created_without_company()
    {
        $response = $this->login(true)->post(route('projects.store'));

        $response->assertInvalid(['company']);
    }

    public function test_project_cannot_be_created_without_user_email()
    {
        $response = $this->login(true)->post(route('projects.store'));

        $response->assertInvalid(['user_email']);
    }

    public function test_project_cannot_be_updated_without_title()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->login(true)->put(route('projects.update', $project));

        $response->assertInvalid(['title']);
    }

    public function test_project_cannot_be_updated_without_deadline()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->login(true)->put(route('projects.update', $project));

        $response->assertInvalid(['deadline']);
    }

    public function test_project_cannot_be_updated_with_misformatted_deadline()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $date = str_replace('-', ' ', now()->addMinutes(30)->toDateString());
        $response = $this->login(true)->put(route('projects.update', $project), [
            'deadline' => $date,
        ]);

        $response->assertInvalid(['deadline']);
    }

    public function test_project_cannot_be_updated_with_past_deadline()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->login(true)->put(route('projects.update', $project), [
            'deadline' => '2001-01-01',
        ]);

        $response->assertInvalid(['deadline']);
    }

    public function test_project_cannot_be_updated_with_invalid_status_number()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->login(true)->put(route('projects.update', $project), [
            'status' => '2',
        ]);

        $response->assertInvalid(['status']);
    }

    public function test_project_cannot_be_updated_without_company()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->login(true)->put(route('projects.update', $project));

        $response->assertInvalid(['company']);
    }

    public function test_project_cannot_be_updated_without_user_email()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->login(true)->put(route('projects.update', $project));

        $response->assertInvalid(['user_email']);
    }

    public function test_guest_cannot_access_index_page()
    {
        $response = $this->get(route('projects.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_creation_page()
    {
        $response = $this->get(route('projects.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_details_page()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->get(route('projects.show', $project));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_edit_page()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->get(route('projects.edit', $project));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_create_projects()
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $response = $this->post(route('projects.store'), [
            'title' => 'test_title',
            'description' => 'test_description',
            'deadline' => '2000-01-01',
            'status' => Project::OPEN_STATUS,
            'company' => $client->company,
            'user_email' => $user->email,
        ]);

        $this->assertDatabaseMissing('projects', [
            'title' => 'test_title',
            'description' => 'test_description',
            'deadline' => '2000-01-01',
            'status' => Project::OPEN_STATUS,
            'client_id' => $client->id,
            'user_id' => $user->id,
        ]);

        $this->assertNull($client->projects()->where('title', 'test_title')->first());
        $this->assertNull($user->projects()->where('title', 'test_title')->first());
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_edit_projects()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $project = Project::factory()
            ->for($client1)
            ->for($user1)
            ->closed()
            ->create();

        $response = $this->put(route('projects.update', $project), [
            'title' => 'edit_' . $project->title,
            'description' => 'edit_' . $project->description,
            'deadline' => '2000-01-01',
            'status' => Project::OPEN_STATUS,
            'company' => $client2->company,
            'user_email' => $user2->email,
        ]);

        $this->assertDatabaseMissing('projects', [
            'title' => 'edit_' . $project->title,
            'description' => 'edit_' . $project->description,
            'deadline' => '2000-01-01',
            'status' => Project::OPEN_STATUS,
            'client_id' => $client2->id,
            'user_id' => $user2->id,
        ]);

        $this->assertDatabaseHas('projects', [
            'title' => $project->title,
            'description' => $project->description,
            'deadline' => $project->deadline,
            'status' => $project->status,
            'client_id' => $client1->id,
            'user_id' => $user1->id,
        ]);

        $this->assertNotNull($client1->projects()->where('title', $project->title)->first());
        $this->assertNotNull($user1->projects()->where('title', $project->title)->first());
        $this->assertNull($client2->projects()->where('title', $project->title)->first());
        $this->assertNull($user2->projects()->where('title', $project->title)->first());
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_delete_projects()
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $project = Project::factory()
            ->for($client)
            ->for($user)
            ->state(['title' => 'test_title'])
            ->create();

        $response = $this->delete(route('projects.destroy', $project));

        $this->assertModelExists($project);
        $this->assertNotNull($client->projects()->where('title', 'test_title')->first());
        $this->assertNotNull($user->projects()->where('title', 'test_title')->first());
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_user_cannot_access_creation_page()
    {
        $response = $this->login()->get(route('projects.create'));

        $response->assertStatus(403);
    }

    public function test_non_admin_user_cannot_access_edit_page()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->login()->get(route('projects.edit', $project));

        $response->assertStatus(403);
    }    

    public function test_non_admin_user_cannot_create_projects()
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $response = $this->login()->post(route('projects.store'), [
            'title' => 'test_title',
            'description' => 'test_description',
            'deadline' => '2000-01-01',
            'status' => Project::OPEN_STATUS,
            'company' => $client->company,
            'user_email' => $user->email,
        ]);

        $this->assertDatabaseMissing('projects', [
            'title' => 'test_title',
            'description' => 'test_description',
            'deadline' => '2000-01-01',
            'status' => Project::OPEN_STATUS,
            'client_id' => $client->id,
            'user_id' => $user->id,
        ]);

        $this->assertNull($client->projects()->where('title', 'test_title')->first());
        $this->assertNull($user->projects()->where('title', 'test_title')->first());
        $response->assertStatus(403);
    }

    public function test_non_admin_user_cannot_edit_projects()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $project = Project::factory()
            ->for($client1)
            ->for($user1)
            ->closed()
            ->create();

        $response = $this->login()->put(route('projects.update', $project), [
            'title' => 'edit_' . $project->title,
            'description' => 'edit_' . $project->description,
            'deadline' => '2000-01-01',
            'status' => Project::OPEN_STATUS,
            'company' => $client2->company,
            'user_email' => $user2->email,
        ]);

        $this->assertDatabaseMissing('projects', [
            'title' => 'edit_' . $project->title,
            'description' => 'edit_' . $project->description,
            'deadline' => '2000-01-01',
            'status' => Project::OPEN_STATUS,
            'client_id' => $client2->id,
            'user_id' => $user2->id,
        ]);

        $this->assertModelExists($project);
        $title = $project->title;
        $this->assertNotNull($client1->projects()->where('title', $title)->first());
        $this->assertNotNull($user1->projects()->where('title', $title)->first());
        $this->assertNull($client2->projects()->where('title', $title)->first());
        $this->assertNull($user2->projects()->where('title', $title)->first());
        $response->assertStatus(403);
    }

    public function test_non_admin_user_cannot_delete_projects()
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $project = Project::factory()
            ->for($client)
            ->for($user)
            ->state(['title' => 'test_title'])
            ->create();

        $response = $this->login()->delete(route('projects.destroy', $project));

        $this->assertModelExists($project);
        $this->assertNotNull($client->projects()->where('title', 'test_title')->first());
        $this->assertNotNull($user->projects()->where('title', 'test_title')->first());
        $response->assertStatus(403);
    }

    public function test_admin_can_access_creation_page()
    {
        $response = $this->login(true)->get(route('projects.create'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_edit_page()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->login(true)->get(route('projects.edit', $project));

        $response->assertStatus(200);
    }    

    public function test_admin_can_create_projects()
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $date = now()->addMinutes(30)->toDateString();
        $response = $this->login(true)->post(route('projects.store'), [
            'title' => 'test_title',
            'description' => 'test_description',
            'deadline' => $date,
            'status' => Project::OPEN_STATUS,
            'company' => $client->company,
            'user_email' => $user->email,
        ]);

        $this->assertDatabaseHas('projects', [
            'title' => 'test_title',
            'description' => 'test_description',
            'deadline' => $date,
            'status' => Project::OPEN_STATUS,
            'client_id' => $client->id,
            'user_id' => $user->id,
        ]);

        $this->assertNotNull($client->projects()->where('title', 'test_title')->first());
        $this->assertNotNull($user->projects()->where('title', 'test_title')->first());
        $project = Project::where('title', 'test_title')->first();
        $response->assertRedirect(route('projects.show', $project));
    }

    public function test_admin_can_edit_projects()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $project = Project::factory()
            ->for($client1)
            ->for($user1)
            ->closed()
            ->create();

        $date = now()->addMinutes(30)->toDateString();
        $response = $this->login(true)->put(route('projects.update', $project), [
            'title' => 'edit_' . $project->title,
            'description' => 'edit_' . $project->description,
            'deadline' => $date,
            'status' => Project::OPEN_STATUS,
            'company' => $client2->company,
            'user_email' => $user2->email,
        ]);

        $this->assertDatabaseHas('projects', [
            'title' => 'edit_' . $project->title,
            'description' => 'edit_' . $project->description,
            'deadline' => $date,
            'status' => Project::OPEN_STATUS,
            'client_id' => $client2->id,
            'user_id' => $user2->id,
        ]);

        $title = 'edit_' . $project->title;
        $this->assertNull($client1->projects()->where('title', $title)->first());
        $this->assertNull($user1->projects()->where('title', $title)->first());
        $this->assertNotNull($client2->projects()->where('title', $title)->first());
        $this->assertNotNull($user2->projects()->where('title', $title)->first());
        $response->assertRedirect(route('projects.show', $project));
    }

    public function test_admin_can_delete_projects()
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $project = Project::factory()
            ->for($client)
            ->for($user)
            ->state(['title' => 'test_title'])
            ->create();

        $response = $this->login(true)->delete(route('projects.destroy', $project));

        $this->assertModelMissing($project);
        $this->assertNull($client->projects()->where('title', 'test_title')->first());
        $this->assertNull($user->projects()->where('title', 'test_title')->first());
        $response->assertRedirect(route('projects.index'));
    }

    public function test_alert_is_sent_to_user_after_they_are_assigned_to_project()
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $date = now()->addMinutes(30)->toDateString();
        $response = $this->login(true)->post(route('projects.store'), [
            'title' => 'test_title',
            'description' => 'test_description',
            'deadline' => $date,
            'status' => Project::OPEN_STATUS,
            'company' => $client->company,
            'user_email' => $user->email,
        ]);

        $this->assertDatabaseHas('projects', [
            'title' => 'test_title',
            'description' => 'test_description',
            'deadline' => $date,
            'status' => Project::OPEN_STATUS,
            'client_id' => $client->id,
            'user_id' => $user->id,
        ]);

        $project = Project::where('title', 'test_title')->first();
        $this->assertDatabaseHas('assignment_alerts', [
            'is_noted' => false,
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);

        $alert = AssignmentAlert::where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->first();
        $this->assertNotNull($project->alerts()->where('project_id', $project->id)->first());
        $this->assertNotNull($user->alerts()->where('user_id', $user->id)->first());
        $this->assertNotNull($alert->project);
        $this->assertNotNull($alert->user);
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
