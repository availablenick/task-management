<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

	public function test_client_page_can_be_rendered()
	{
		$response = $this->get('/clients');

		$response->assertStatus(200);
	}

	public function test_client_details_page_can_be_rendered()
	{
		$client = Client::factory()->create();
		$response = $this->get('/clients/' . $client->id);

		$response->assertStatus(200);
	}

	public function test_client_cannot_be_created_without_company()
	{
		$response = $this->login(true)->post('/clients');
		
		$response->assertInvalid(['company']);
	}

	public function test_client_cannot_be_created_without_vat()
	{
		$response = $this->login(true)->post('/clients');
		
		$response->assertInvalid(['vat']);
	}

	public function test_client_cannot_be_created_with_non_integer_vat()
	{
		$response = $this->login(true)->post('/clients', [
			'vat' => '1.5',
		]);

		$response->assertInvalid(['vat']);
	}

	public function test_client_cannot_be_created_without_address()
	{
		$response = $this->login(true)->post('/clients');

		$response->assertInvalid(['address']);
	}

	public function test_client_cannot_be_created_with_non_boolean_active_flag()
	{
		$response = $this->login(true)->post('/clients', [
			'is_active' => 'true',
		]);

		$response->assertInvalid(['is_active']);
	}

	public function test_client_cannot_be_updated_with_non_integer_vat()
	{
		$client = Client::factory()->create();
		$response = $this->login(true)->put('/clients/' . $client->id, [
			'vat' => '1.5',
		]);

		$response->assertInvalid(['vat']);
	}

	public function test_client_cannot_be_updated_with_non_boolean_active_flag()
	{
		$client = Client::factory()->create();
		$response = $this->login(true)->put('/clients/' . $client->id, [
			'is_active' => 'true',
		]);

		$response->assertInvalid(['is_active']);
	}

	public function test_guest_cannot_access_creation_page()
	{
		$response = $this->get('/clients/create');

		$response->assertRedirect('/login');
	}

	public function test_guest_cannot_access_edit_page()
	{
		$client = Client::factory()->create();
		$response = $this->get('/clients/' . $client->id . '/edit');

		$response->assertRedirect('/login');
	}

	public function test_guest_cannot_create_clients()
	{
		$response = $this->post('/clients', [
			'company' => 'test_company',
			'vat' => 12345,
			'address' => 'test_address',
			'is_active' => true,
		]);

		$this->assertDatabaseMissing('clients', [
			'company' => 'test_company',
			'vat' => 12345,
			'address' => 'test_address',
			'is_active' => true,
		]);

		$response->assertRedirect('/login');
	}

	public function test_guest_cannot_edit_clients()
	{
		$client = Client::factory()->create();
		$newData = [
			'company' => 'edit_' . $client->company,
			'vat' => $client->vat + 1,
			'address' => 'edit_' . $client->address,
			'is_active' => !$client->is_active,
		];

		$response = $this->put('/clients/' . $client->id, $newData);

		$this->assertDatabaseMissing('clients', $newData);
		$response->assertRedirect('/login');
	}

	public function test_guest_cannot_delete_clients()
	{
		$client = Client::factory()->create();
		$response = $this->delete('/clients/' . $client->id);

		$this->assertModelExists($client);
		$response->assertRedirect('/login');
	}

	public function test_non_admin_user_cannot_access_creation_page()
	{
		$response = $this->login()->get('/clients/create');

		$response->assertRedirect('/unauthorized');
	}

	public function test_non_admin_user_cannot_access_edit_page()
	{
		$client = Client::factory()->create();
		$response = $this->login()->get('/clients/' . $client->id . '/edit');

		$response->assertRedirect('/unauthorized');
	}

	public function test_non_admin_user_cannot_create_clients()
	{
		$response = $this->login()->post('/clients', [
			'company' => 'test_company',
			'vat' => 12345,
			'address' => 'test_address',
			'is_active' => true,
		]);

		$this->assertDatabaseMissing('clients', [
			'company' => 'test_company',
			'vat' => 12345,
			'address' => 'test_address',
			'is_active' => true,
		]);

		$response->assertRedirect('/unauthorized');
	}

	public function test_non_admin_user_cannot_edit_clients()
	{
		$client = Client::factory()->create();
		$newData = [
			'company' => 'edit_' . $client->company,
			'vat' => $client->vat + 1,
			'address' => 'edit_' . $client->address,
			'is_active' => !$client->is_active,
		];

		$response = $this->login()->put('/clients/' . $client->id, $newData);

		$this->assertDatabaseMissing('clients', $newData);
		$this->assertModelExists($client);
		$response->assertRedirect('/unauthorized');
	}

	public function test_non_admin_user_cannot_delete_clients()
	{
		$client = Client::factory()->create();
		$response = $this->login()->delete('/clients/' . $client->id);

		$this->assertModelExists($client);
		$response->assertRedirect('/unauthorized');
	}

	public function test_admin_can_access_creation_page()
	{
		$response = $this->login(true)->get('/clients/create');

		$response->assertStatus(200);
	}

	public function test_admin_can_access_edit_page()
	{
		$client = Client::factory()->create();
		$response = $this->login(true)->get('/clients/' . $client->id . '/edit');

		$response->assertStatus(200);
	}

	public function test_admin_can_create_clients()
	{
		$response = $this->login(true)->post('/clients', [
			'company' => 'test_company',
			'vat' => 12345,
			'address' => 'test_address',
			'is_active' => true,
		]);

		$this->assertDatabaseHas('clients', [
			'company' => 'test_company',
			'vat' => 12345,
			'address' => 'test_address',
			'is_active' => true,
		]);

		$client = Client::where('company', 'test_company')->first();
		$response->assertRedirect('/clients/' . $client->id);
	}

	public function test_admin_can_edit_clients()
	{
		$client = Client::factory()->create();
		$newData = [
			'company' => 'edit_' . $client->company,
			'vat' => $client->vat + 1,
			'address' => 'edit_' . $client->address,
			'is_active' => !$client->is_active,
		];

		$response = $this->login(true)->put('/clients/' . $client->id, $newData);

		$this->assertDatabaseHas('clients', $newData);
		$response->assertRedirect('/clients/' . $client->id);
	}

	public function test_admin_can_delete_clients()
	{
		$client = Client::factory()->create();
		$response = $this->login(true)->delete('/clients/' . $client->id);

		$this->assertModelMissing($client);
		$response->assertRedirect('/clients');
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