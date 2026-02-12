<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RbacSecurityNonRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('app:setup-roles-permissions --force');
    }

    public function test_stats_api_access_matrix(): void
    {
        foreach (['admin', 'direction', 'gestionnaire', 'comptable'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $user->assignRole($role);

            $this->actingAs($user)
                ->get('/api/stats/kpis')
                ->assertStatus(200);
        }

        $proprietaire = User::factory()->create(['role' => 'proprietaire']);
        Role::findOrCreate('proprietaire', 'web');
        $proprietaire->assignRole('proprietaire');

        $this->actingAs($proprietaire)
            ->get('/api/stats/kpis')
            ->assertStatus(403);
    }

    public function test_direction_cannot_mutate_biens(): void
    {
        $direction = User::factory()->create(['role' => 'direction']);
        $direction->assignRole('direction');

        $this->actingAs($direction)
            ->post(route('dashboard.biens.store'), [])
            ->assertStatus(403);
    }

    public function test_role_without_permission_is_blocked_on_sensitive_route(): void
    {
        $gestionnaireRole = Role::findByName('gestionnaire');
        $savedPermissions = $gestionnaireRole->permissions->pluck('name')->all();

        $gestionnaireRole->syncPermissions([]);

        $gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
        $gestionnaire->assignRole('gestionnaire');

        $this->actingAs($gestionnaire)
            ->post(route('dashboard.biens.store'), [])
            ->assertStatus(403);

        $gestionnaireRole->syncPermissions($savedPermissions);
    }
}
