<?php

namespace Tests\Feature;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ContratRbacTest extends TestCase
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
    public function test_admin_can_create_contrat()
    {
        $bien = Bien::factory()->create(['statut' => 'libre']);
        $locataire = Locataire::factory()->create();

        $response = $this->actingAs($this->admin)->postJson(route('contrats.store'), [
            'bien_id' => $bien->id,
            'locataire_id' => $locataire->id,
            'date_debut' => now()->format('Y-m-d'),
            'loyer_montant' => 150000,
            'type_bail' => 'habitation',
            'statut' => 'actif',
        ]);

        $response->assertStatus(201);
    }

    public function test_gestionnaire_can_create_contrat()
    {
        $bien = Bien::factory()->create(['statut' => 'libre']);
        $locataire = Locataire::factory()->create();

        $response = $this->actingAs($this->gestionnaire)->postJson(route('contrats.store'), [
            'bien_id' => $bien->id,
            'locataire_id' => $locataire->id,
            'date_debut' => now()->format('Y-m-d'),
            'loyer_montant' => 150000,
            'type_bail' => 'habitation',
            'statut' => 'actif',
        ]);

        $response->assertStatus(201);
    }

    public function test_comptable_cannot_create_contrat()
    {
        $response = $this->actingAs($this->comptable)->postJson(route('contrats.store'), [
            'loyer_montant' => 1000,
        ]);

        $response->assertStatus(403);
    }

    public function test_direction_cannot_create_contrat()
    {
        $response = $this->actingAs($this->direction)->postJson(route('contrats.store'), [
            'loyer_montant' => 1000,
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test UPDATE access
     */
    public function test_gestionnaire_can_update_contrat()
    {
        $contrat = Contrat::factory()->create();

        $response = $this->actingAs($this->gestionnaire)->putJson(route('contrats.update', $contrat), [
            'bien_id' => $contrat->bien_id,
            'locataire_id' => $contrat->locataire_id,
            'loyer_montant' => 200000,
            'date_debut' => $contrat->date_debut->format('Y-m-d'),
            'type_bail' => 'habitation',
            'statut' => 'actif',
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test DELETE access
     */
    public function test_gestionnaire_can_delete_contrat()
    {
        $contrat = Contrat::factory()->create();

        $response = $this->actingAs($this->gestionnaire)->deleteJson(route('contrats.destroy', $contrat));

        $response->assertStatus(200);
    }

    public function test_comptable_cannot_delete_contrat()
    {
        $contrat = Contrat::factory()->create();

        $response = $this->actingAs($this->comptable)->deleteJson(route('contrats.destroy', $contrat));

        $response->assertStatus(403);
    }

    /**
     * Test PRINT access
     */
    public function test_direction_can_print_contrat()
    {
        $contrat = Contrat::factory()->create();
        
        $response = $this->actingAs($this->direction)->get(route('contrats.print', $contrat));

        // 200 OK means authorized (PDF generation is complex to mock, but status is enough)
        $response->assertStatus(200);
    }
}
