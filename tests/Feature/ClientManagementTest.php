<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

	public function test_client_list_page_can_be_rendered()
	{
		$response = $this->login()->get(route('clients.index'));

		$response->assertStatus(200);
	}

	public function test_client_creation_page_can_be_rendered()
	{
		$response = $this->login(true)->get(route('clients.create'));

		$response->assertStatus(200);
	}

	public function test_client_details_page_can_be_rendered()
	{
		$client = Client::factory()->create();
		$response = $this->login()->get(route('clients.show', $client));

		$response->assertStatus(200);
	}

	public function test_client_edit_page_can_be_rendered()
	{
		$client = Client::factory()->create();
		$response = $this->login(true)->get(route('clients.edit', $client));

		$response->assertStatus(200);
	}

	public function test_client_cannot_be_created_without_company()
	{
		$response = $this->login(true)->post(route('clients.store'));
		
		$response->assertInvalid(['company']);
	}

	public function test_client_cannot_be_created_without_vat()
	{
		$response = $this->login(true)->post(route('clients.store'));
		
		$response->assertInvalid(['vat']);
	}

	public function test_client_cannot_be_created_with_non_integer_vat()
	{
		$response = $this->login(true)->post(route('clients.store'), [
			'vat' => '1.5',
		]);

		$response->assertInvalid(['vat']);
	}

	public function test_client_cannot_be_created_without_address()
	{
		$response = $this->login(true)->post(route('clients.store'));

		$response->assertInvalid(['address']);
	}

	public function test_client_cannot_be_created_with_non_boolean_active_flag()
	{
		$response = $this->login(true)->post(route('clients.store'), [
			'is_active' => 'true',
		]);

		$response->assertInvalid(['is_active']);
	}

	public function test_client_cannot_be_updated_without_company()
	{
		$client = Client::factory()->create();
		$response = $this->login(true)->put(route('clients.update', $client));
		
		$response->assertInvalid(['company']);
	}

	public function test_client_cannot_be_updated_without_vat()
	{
		$client = Client::factory()->create();
		$response = $this->login(true)->put(route('clients.update', $client));
		
		$response->assertInvalid(['vat']);
	}

	public function test_client_cannot_be_updated_with_non_integer_vat()
	{
		$client = Client::factory()->create();
		$response = $this->login(true)->put(route('clients.update', $client), [
			'vat' => '1.5',
		]);

		$response->assertInvalid(['vat']);
	}

	public function test_client_cannot_be_updated_without_address()
	{
		$client = Client::factory()->create();
		$response = $this->login(true)->put(route('clients.update', $client));

		$response->assertInvalid(['address']);
	}

	public function test_client_cannot_be_updated_with_non_boolean_active_flag()
	{
		$client = Client::factory()->create();
		$response = $this->login(true)->put(route('clients.update', $client), [
			'is_active' => 'true',
		]);

		$response->assertInvalid(['is_active']);
	}
	
	public function test_guest_cannot_access_index_page()
	{
		$response = $this->get(route('clients.index'));

		$response->assertRedirect(route('login'));
	}

	public function test_guest_cannot_access_creation_page()
	{
		$response = $this->get(route('clients.create'));

		$response->assertRedirect(route('login'));
	}

	public function test_guest_cannot_access_details_page()
	{
		$client = Client::factory()->create();
		$response = $this->get(route('clients.show', $client));

		$response->assertRedirect(route('login'));
	}

	public function test_guest_cannot_access_edit_page()
	{
		$client = Client::factory()->create();
		$response = $this->get(route('clients.edit', $client));

		$response->assertRedirect(route('login'));
	}

	public function test_guest_cannot_create_clients()
	{
		$response = $this->post(route('clients.store'), [
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

		$response->assertRedirect(route('login'));
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

		$response = $this->put(route('clients.update', $client), $newData);

		$this->assertDatabaseMissing('clients', $newData);
		$response->assertRedirect(route('login'));
	}

	public function test_guest_cannot_delete_clients()
	{
		$client = Client::factory()->create();
		$response = $this->delete(route('clients.destroy', $client));

		$this->assertModelExists($client);
		$response->assertRedirect(route('login'));
	}

	public function test_non_admin_user_cannot_access_creation_page()
	{
		$response = $this->login()->get(route('clients.create'));

		$response->assertStatus(403);
	}

	public function test_non_admin_user_cannot_access_edit_page()
	{
		$client = Client::factory()->create();
		$response = $this->login()->get(route('clients.edit', $client));

		$response->assertStatus(403);
	}

	public function test_non_admin_user_cannot_create_clients()
	{
		$response = $this->login()->post(route('clients.store'), [
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

		$response->assertStatus(403);
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

		$response = $this->login()->put(route('clients.update', $client), $newData);

		$this->assertDatabaseMissing('clients', $newData);
		$this->assertModelExists($client);
		$response->assertStatus(403);
	}

	public function test_non_admin_user_cannot_delete_clients()
	{
		$client = Client::factory()->create();
		$response = $this->login()->delete(route('clients.destroy', $client));

		$this->assertModelExists($client);
		$response->assertStatus(403);
	}

	public function test_admin_can_access_creation_page()
	{
		$response = $this->login(true)->get(route('clients.create'));

		$response->assertStatus(200);
	}

	public function test_admin_can_access_edit_page()
	{
		$client = Client::factory()->create();
		$response = $this->login(true)->get(route('clients.edit', $client));

		$response->assertStatus(200);
	}

	public function test_admin_can_create_clients()
	{
		$response = $this->login(true)->post(route('clients.store'), [
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
		$response->assertRedirect(route('clients.show', $client));
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

		$response = $this->login(true)->put(route('clients.update', $client), $newData);

		$this->assertDatabaseHas('clients', $newData);
		$response->assertRedirect(route('clients.show', $client));
	}

	public function test_admin_can_delete_clients()
	{
		$client = Client::factory()->create();
		$response = $this->login(true)->delete(route('clients.destroy', $client));

		$this->assertModelMissing($client);
		$response->assertRedirect(route('clients.index'));
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