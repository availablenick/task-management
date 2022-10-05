<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_guest_cannot_access_dashboard_page()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}
