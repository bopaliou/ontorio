<?php

namespace Tests\Feature;

use App\Models\Proprietaire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProprietaireRbacTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $gestionnaire;
    private $comptable;
    private $direction;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles and permissions
        $this->artisan('app:setup-roles-permissions --force');

        // Create users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');

        $this->gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
        $this->gestionnaire->assignRole('gestionnaire');

        $this->comptable = User::factory()->create(['role' => 'comptable']);
        $this->comptable->assignRole('comptable');

        $this->direction = User::factory()->create(['role' => 'direction']);
        $this->direction->assignRole('direction');
    }

    /**
     * Test CREATE access
     */
    public function test_admin_can_create_proprietaire()
    {
        $response = $this->actingAs($this->admin)->postJson(route('proprietaires.store'), [
            'nom' => 'Proprietaire Admin',
            'email' => 'admin.prop@test.com',
            'telephone' => '770000001',
        ]);

        $response->assertStatus(201);
    }

    public function test_gestionnaire_can_create_proprietaire()
    {
        $response = $this->actingAs($this->gestionnaire)->postJson(route('proprietaires.store'), [
            'nom' => 'Proprietaire Gestionnaire',
            'email' => 'gest.prop@test.com',
            'telephone' => '770000002',
        ]);

        $response->assertStatus(201);
    }

    public function test_comptable_cannot_create_proprietaire()
    {
        $response = $this->actingAs($this->comptable)->postJson(route('proprietaires.store'), [
            'nom' => 'Proprietaire Interdit',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test UPDATE access
     */
    public function test_gestionnaire_can_update_proprietaire()
    {
        $prop = Proprietaire::factory()->create();

        $response = $this->actingAs($this->gestionnaire)->putJson(route('proprietaires.update', $prop), [
            'nom' => 'Nom Modifié',
            'email' => $prop->email,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test DELETE access
     */
    public function test_admin_can_delete_proprietaire()
    {
        $prop = Proprietaire::factory()->create();

        $response = $this->actingAs($this->admin)->deleteJson(route('proprietaires.destroy', $prop));

        $response->assertStatus(200);
    }

    public function test_gestionnaire_cannot_delete_proprietaire()
    {
        $prop = Proprietaire::factory()->create();

        $response = $this->actingAs($this->gestionnaire)->deleteJson(route('proprietaires.destroy', $prop));

        // According to SetupRolesAndPermissions, gestionnaire doesn't have proprietaires.delete
        $response->assertStatus(403);
    }

    /**
     * Test BILAN access
     */
    public function test_direction_can_access_bilan_proprietaire()
    {
        $prop = Proprietaire::factory()->create();
        
        $response = $this->actingAs($this->direction)->get(route('proprietaires.bilan', $prop));

        $response->assertStatus(200);
    }

    public function test_comptable_can_access_bilan_proprietaire()
    {
        $prop = Proprietaire::factory()->create();
        
        $response = $this->actingAs($this->comptable)->get(route('proprietaires.bilan', $prop));

        $response->assertStatus(200);
    }
}
