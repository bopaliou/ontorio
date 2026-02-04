<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed basic roles for testing
        // On check if roles exist to avoid duplicates if seed ran
        if (Role::count() === 0) {
            Role::create(['name' => 'admin']);
            Role::create(['name' => 'gestionnaire']);
            Role::create(['name' => 'comptable']);
            Role::create(['name' => 'direction']);
        }
    }

    public function test_guest_redirected_to_login()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));
    }

    public function test_admin_access_dashboard_200()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
        $response->assertViewHas('data');
    }

    public function test_gestionnaire_access_dashboard_200()
    {
        $user = User::factory()->create([
            'role' => 'gestionnaire'
        ]);
        $user->assignRole('gestionnaire');

        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
    }
}
