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

    public function test_task_page_can_be_rendered()
    {
        $response = $this->get('/tasks');

        $response->assertStatus(200);
    }

    public function test_task_details_page_can_be_rendered()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $task = Task::factory()->for($project)->create();
        $response = $this->get('/tasks');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_creation_page()
    {
        $response = $this->get('/tasks/create');

        $response->assertRedirect('/login');
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
        $response = $this->get('/tasks/' . $task->id . '/edit');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_create_tasks()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $response = $this->post('/tasks', [
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
        $response->assertRedirect('/login');
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
        $response = $this->put('/tasks/' . $task->id, [
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
        $response->assertRedirect('/login');
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
        $response = $this->delete('/tasks/' . $task->id);

        $this->assertModelExists($task);
        $this->assertNotNull($project->tasks()->where('title', 'test_title')->first());
        $response->assertRedirect('/login');
    }

    public function test_non_assigned_user_cannot_create_tasks()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user1)
            ->create();

        $response = $this->actingAs($user2)->post('/tasks', [
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
        $response->assertRedirect('/unauthorized');
    }

    public function test_non_assigned_user_cannot_edit_tasks()
    {
        $project = Project::factory()
            ->for(Client::factory())
            ->for(User::factory())
            ->create();

        $task = Task::factory()->for($project)->create();
        $response = $this->login()->put('/tasks/' . $task->id, [
            'title' => 'edit_' . $task->title,
            'description' => 'edit_' . $task->description,
        ]);

        $this->assertDatabaseMissing('tasks', [
            'title' => 'edit_' . $task->title,
            'description' => 'edit_' . $task->description,
        ]);

        $this->assertNull($project->tasks()->where('title', 'edit_' . $task->title)->first());
        $response->assertRedirect('/unauthorized');
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
        $response = $this->login()->delete('/tasks/' . $task->id);

        $this->assertModelExists($task);
        $this->assertNotNull($project->tasks()->where('title', 'test_title')->first());
        $response->assertRedirect('/unauthorized');
    }

    public function test_assigned_user_can_create_tasks()
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

        $response = $this->actingAs($user)->post('/tasks', [
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
        $response->assertRedirect('/tasks/' . $task->id);
    }

    public function test_assigned_user_can_edit_tasks()
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

        $task = Task::factory()->for($project)->create();
        $response = $this->actingAs($user)->put('/tasks/' . $task->id, [
            'title' => 'edit_' . $task->title,
            'description' => 'edit_' . $task->description,
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'edit_' . $task->title,
            'description' => 'edit_' . $task->description,
        ]);

        $this->assertNotNull($project->tasks()->where('title', 'edit_' . $task->title)->first());
        $response->assertRedirect('/tasks/' . $task->id);
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
        $response = $this->actingAs($user)->delete('/tasks/' . $task->id);

        $this->assertModelMissing($task);
        $this->assertNull($project->tasks()->where('title', 'test_title')->first());
        $response->assertRedirect('/tasks');
    }

    /*
        Helpers
    */
    private function login()
	{
		return $this->actingAs(User::factory()->create());
	}
}
