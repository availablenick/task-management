<?php

namespace Tests\Feature;

use App\Models\AssignmentAlert;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AssignmentAlertManagementTest extends TestCase
{
	use RefreshDatabase;

	public function test_alert_list_page_can_be_rendered()
	{
		$user = User::factory()->create();
		$response = $this->actingAs($user)->get(route('assignment_alerts.index'));

		$response->assertStatus(200);
	}

	public function test_guest_cannot_access_index_page()
	{
		$response = $this->get(route('assignment_alerts.index'));

		$response->assertRedirect(route('login'));
	}

	public function test_guest_cannot_update_multiple_alerts()
	{
		$user = User::factory()->create();
		$project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

		$alert = AssignmentAlert::factory()
			->count(5)
			->for($project)
			->for($user)
			->create();

		$response = $this->post(route('assignment_alerts.note'));

		$response->assertRedirect(route('login'));
	}

	public function test_multiple_user_alerts_can_be_updated_at_once()
	{
		$user = User::factory()->create();
		$project = Project::factory()
            ->for(Client::factory())
            ->for($user)
            ->create();

		$alert = AssignmentAlert::factory()
			->count(5)
			->for($project)
			->for($user)
			->create();

		$response = $this->actingAs($user)->post(route('assignment_alerts.note'));

		$this->assertEquals(0, $user->alerts()->where('is_noted', false)->count());
		$this->assertEquals(6, $user->alerts()->where('is_noted', true)->count());
		$response->assertStatus(200);
	}
}
