<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_list_page_can_be_rendered()
    {
        $response = $this->login()->get(route('tasks.index'));

        $response->assertStatus(200);
    }

    public function test_task_creation_page_can_be_rendered()
    {
        $response = $this->login()->get(route('tasks.create'));

        $response->assertStatus(200);
    }

    public function test_task_details_page_can_be_rendered()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $task = Task::factory()->for($project)->create();
        $response = $this->login()->get(route('tasks.show', $task));

        $response->assertStatus(200);
    }

    public function test_task_edit_page_can_be_rendered()
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

        $task = Task::factory()->for($project)->create();
        $response = $this->actingAs($user)->get(route('tasks.edit', $task));

        $response->assertStatus(200);
    }

    public function test_task_cannot_be_created_without_title()
    {
        $response = $this->login()->post(route('tasks.store'));

        $response->assertInvalid(['title']);
    }

    public function test_task_cannot_be_created_without_project_title()
    {
        $response = $this->login()->post(route('tasks.store'));

        $response->assertInvalid(['project_title']);
    }

    public function test_task_cannot_be_created_with_existing_title_and_project_title_pair()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $task = Task::factory()->for($project)->create();
        $response = $this->login()->post(route('tasks.store'), [
            'title' => $task->title,
            'project_title' => $project->title,
        ]);

        $response->assertInvalid(['title']);
    }

    public function test_task_cannot_be_updated_without_title()
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

        $task = Task::factory()->for($project)->create();
        $response = $this->actingAs($user)->put(route('tasks.update', $task));

        $response->assertInvalid(['title']);
    }

    public function test_task_cannot_be_updated_with_existing_title_and_project_title_pair()
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

        $task1 = Task::factory()->for($project)->create();
        $task2 = Task::factory()->for($project)->create();
        $response = $this->actingAs($user)->put(route('tasks.update', $task1), [
            'title' => $task2->title,
        ]);

        $response->assertInvalid(['title']);
    }

    public function test_guest_cannot_access_index_page()
    {
        $response = $this->get(route('tasks.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_creation_page()
    {
        $response = $this->get(route('tasks.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_details_page()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $task = Task::factory()->for($project)->create();
        $response = $this->get(route('tasks.show', $project));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_edit_page()
    {
        $project1 = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();
        $project2 = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $task = Task::factory()->for($project1)->create();
        $response = $this->get(route('tasks.edit', $task));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_create_tasks()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->post(route('tasks.store'), [
            'title' => 'test_title',
            'description' => 'test_description',
            'project_title' => $project->title,
        ]);

        $this->assertDatabaseMissing('tasks', [
            'title' => 'test_title',
            'description' => 'test_description',
            'project_id' => $project->id,
        ]);

        $this->assertNull($project->tasks()->where('title', 'test_title')->first());
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_edit_tasks()
    {
        $project1 = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();
        $project2 = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $task = Task::factory()->for($project1)->create();
        $response = $this->put(route('tasks.update', $task), [
            'title' => 'edit_' . $task->title,
            'description' => 'edit_' . $task->description,
            'project_title' => $project2->title,
        ]);

        $this->assertDatabaseMissing('tasks', [
            'title' => 'edit_' . $task->title,
            'description' => 'edit_' . $task->description,
            'project_id' => $project2->id,
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => $task->title,
            'description' => $task->description,
            'project_id' => $project1->id,
        ]);

        $this->assertNotNull($project1->tasks()->where('title', $task->title)->first());
        $this->assertNull($project2->tasks()->where('title', $task->title)->first());
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_destroy_tasks()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $task = Task::factory()
            ->for($project)
            ->state(['title' => 'test_title'])
            ->create();
        $response = $this->delete(route('tasks.destroy', $task));

        $this->assertModelExists($task);
        $this->assertNotNull($project->tasks()->where('title', 'test_title')->first());
        $response->assertRedirect(route('login'));
    }

    public function test_non_assigned_user_cannot_access_edit_page()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user1)
            ->create();

        $task = Task::factory()->for($project)->create();
        $response = $this->actingAs($user2)->get(route('tasks.edit', $project));

        $response->assertStatus(403);
    }

    public function test_non_assigned_user_cannot_create_tasks()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user1)
            ->create();

        $response = $this->actingAs($user2)->post(route('tasks.store'), [
            'title' => 'test_title',
            'description' => 'test_description',
            'project_title' => $project->title,
        ]);

        $this->assertDatabaseMissing('tasks', [
            'title' => 'test_title',
            'description' => 'test_description',
            'project_id' => $project->id,
        ]);

        $this->assertNull($project->tasks()->where('title', 'test_title')->first());
        $response->assertStatus(403);
    }

    public function test_non_assigned_user_cannot_edit_tasks()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $task = Task::factory()->for($project)->create();
        $response = $this->login()->put(route('tasks.update', $task), [
            'title' => 'edit_' . $task->title,
            'description' => 'edit_' . $task->description,
        ]);

        $this->assertDatabaseMissing('tasks', [
            'title' => 'edit_' . $task->title,
            'description' => 'edit_' . $task->description,
        ]);

        $this->assertNull($project->tasks()->where('title', 'edit_' . $task->title)->first());
        $response->assertStatus(403);
    }

    public function test_non_assigned_user_cannot_delete_tasks()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $task = Task::factory()
            ->for($project)
            ->state(['title' => 'test_title'])
            ->create();
        $response = $this->login()->delete(route('tasks.destroy', $task));

        $this->assertModelExists($task);
        $this->assertNotNull($project->tasks()->where('title', 'test_title')->first());
        $response->assertStatus(403);
    }

    public function test_assigned_user_can_create_tasks()
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'test_title',
            'description' => 'test_description',
            'project_title' => $project->title,
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'test_title',
            'description' => 'test_description',
            'project_id' => $project->id,
        ]);

        $task = Task::where('title', 'test_title')->first();
        $this->assertEquals($project->title, $task->project->title);
        $this->assertNotNull($project->tasks()->where('title', 'test_title')->first());
        $response->assertRedirect(route('tasks.show', $task));
    }

    public function test_assigned_user_can_edit_tasks()
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

        $task = Task::factory()->for($project)->create();
        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'edit_' . $task->title,
            'description' => 'edit_' . $task->description,
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'edit_' . $task->title,
            'description' => 'edit_' . $task->description,
        ]);

        $this->assertNotNull($project->tasks()->where('title', 'edit_' . $task->title)->first());
        $response->assertRedirect(route('tasks.show', $task));
    }

    public function test_assigned_user_can_delete_tasks()
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

        $task = Task::factory()
            ->for($project)
            ->state(['title' => 'test_title'])
            ->create();
        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $this->assertModelMissing($task);
        $this->assertNull($project->tasks()->where('title', 'test_title')->first());
        $response->assertRedirect(route('tasks.index'));
    }

    public function test_unverified_user_cannot_access_creation_page()
    {
        $user = User::factory()->unverified()->create();
        $response = $this->actingAs($user)->get(route('tasks.create'));

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_user_cannot_access_edit_page()
    {
        $user = User::factory()->unverified()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

        $task = Task::factory()->for($project)->create();
        $response = $this->actingAs($user)->get(route('tasks.edit', $task));

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_user_cannot_create_tasks()
    {
        $user = User::factory()->unverified()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'test_title',
            'description' => 'test_description',
            'project_title' => $project->title,
        ]);

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_user_cannot_edit_tasks()
    {
        $user = User::factory()->unverified()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

        $task = Task::factory()->for($project)->create();
        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'edit_' . $task->title,
            'description' => 'edit_' . $task->description,
        ]);

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_user_cannot_delete_tasks()
    {
        $user = User::factory()->unverified()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

        $task = Task::factory()
            ->for($project)
            ->state(['title' => 'test_title'])
            ->create();
        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('verification.notice'));
    }

    /*
        Helpers
    */
    private function login()
	{
		return $this->actingAs(User::factory()->create());
	}
}
