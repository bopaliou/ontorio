<?php

namespace Tests\Feature;

error_log('AdminAccessTest.php LOADED');

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialisation complète des rôles et permissions Spatie
        \Illuminate\Support\Facades\Artisan::call('app:setup-roles-permissions');
    }

    public function test_admin_section_hidden_for_unauthorized_users()
    {
        $user = User::factory()->create(['role' => 'comptable']);
        $user->assignRole('comptable');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('id="section-admin"', false);
    }

    public function test_admin_section_visible_for_authorized_users()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('id="section-admin"', false);
    }
}
