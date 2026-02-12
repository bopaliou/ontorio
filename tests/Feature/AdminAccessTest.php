<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Artisan::call('app:setup-roles-permissions');
    }

    public function test_admin_section_hidden_for_unauthorized_users()
    {
        // User without users.view permission (e.g. standard owner or simple employee)
        // Adjust role to one that definitely doesn't have it, or create a custom one if possible,
        // but let's use 'locataire' or similar if they exist, or just 'user' with no role?
        // 'gestionnaire' usually has permissions.
        // Let's use a role that is NOT admin and NOT having 'users.view'.
        // Assuming 'comptable' doesn't have 'users.view'.

        $user = User::factory()->create(['role' => 'comptable']);
        $user->assignRole('comptable');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);

        // Should NOT see Admin section IDs
        $response->assertDontSee('id="section-admin"', false);
        $response->assertDontSee('id="nav-link-utilisateurs"', false);
        $response->assertDontSee('id="section-utilisateurs"', false);
    }

    public function test_admin_section_visible_for_authorized_users()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);

        // Check for presence of admin UI elements (labels/buttons present in dashboard)
        $content = $response->getContent();
        $found = str_contains($content, 'Utilisateurs') || str_contains($content, 'Rôles & Accès') || str_contains($content, '/users') || str_contains($content, 'settings/roles');
        $this->assertTrue($found, 'Admin dashboard missing expected admin links or labels');
    }
}
