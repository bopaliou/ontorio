<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_links_contain_dashboard_route_and_hash()
    {
        // Create an admin user to see all links
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);

        // Verify Dashboard Link
        $response->assertSee(route('dashboard').'#overview');

        // Verify Gestion Links
        $response->assertSee(route('dashboard').'#proprietaires');
        $response->assertSee(route('dashboard').'#biens');
        $response->assertSee(route('dashboard').'#locataires');
        $response->assertSee(route('dashboard').'#contrats');

        // Verify Finance Links
        $response->assertSee(route('dashboard').'#loyers');
        $response->assertSee(route('dashboard').'#paiements');
        $response->assertSee(route('dashboard').'#depenses');

        // Verify Admin Links
        $response->assertSee(route('dashboard').'#utilisateurs');
        $response->assertSee(route('dashboard').'#logs');

        // Verify Config Link
        $response->assertSee(route('dashboard').'#parametres');
    }

    public function test_sidebar_config_hidden_for_non_admin()
    {
        $user = User::factory()->create(['role' => 'gestionnaire']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);

        // Config section should NOT be visible
        $response->assertDontSee('id="section-config"', false);
        // Note: We might see the word "Config" if it's in a JS script or comment,
        // but we shouldn't see the HTML structure for the link if we wrapped it correctly.
        $response->assertDontSee(route('dashboard').'#parametres');
    }
}
