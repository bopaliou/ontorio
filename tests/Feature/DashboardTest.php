<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_correctly_for_authenticated_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);

        // Verify Overview section exists (default view)
        $response->assertSee('id="section-overview"', false);

        // Verify common components loaded
        $response->assertSee('OntarioDashboard'); // JS Class
    }

    public function test_dashboard_redirects_guests()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}
