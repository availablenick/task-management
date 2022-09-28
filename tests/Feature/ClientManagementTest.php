<?php

namespace Tests\Feature;

use App\Models\Client;
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

	public function test_client_can_be_created()
	{
		$response = $this->post('/clients', [
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

	public function test_client_can_be_updated()
	{
		$client = Client::factory()->create();
		$newData = [
			'company' => 'edit_' . $client->company,
			'vat' => $client->vat + 1,
			'address' => 'edit_' . $client->address,
			'is_active' => !$client->is_active,
		];

		$response = $this->put('/clients/' . $client->id, $newData);

		$this->assertDatabaseHas('clients', $newData);
		$response->assertRedirect('/clients/' . $client->id);
	}

	public function test_client_can_be_deleted()
	{
		$client = Client::factory()->create();
		$response = $this->delete('/clients/' . $client->id);

		$this->assertModelMissing($client);
		$response->assertRedirect('/clients');
	}
}