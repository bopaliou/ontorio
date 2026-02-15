<?php

namespace Tests\Feature;

use App\Models\Locataire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocataireTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_locataire(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('locataires.store'), [
                'nom' => 'Jean Dupont',
                'email' => 'jean.dupont@example.com',
                'telephone' => '771234567',
                'adresse' => 'Dakar, Plateau',
                'cni' => '1234567890123',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('locataires', [
            'nom' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
        ]);
    }

    public function test_locataire_validation_fails(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('locataires.store'), [
                'nom' => '',
                'email' => 'not-an-email',
                'telephone' => '771234567',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nom', 'email']);
    }

    public function test_admin_can_update_locataire(): void
    {
        $locataire = Locataire::factory()->create([
            'nom' => 'Ancien Nom',
            'email' => 'old@example.com',
        ]);

        $response = $this->actingAs($this->admin)
            ->putJson(route('locataires.update', $locataire), [
                'nom' => 'Nouveau Nom',
                'email' => 'new@example.com',
                'telephone' => '781234567',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('locataires', [
            'id' => $locataire->id,
            'nom' => 'Nouveau Nom',
            'email' => 'new@example.com',
        ]);
    }

    public function test_admin_can_delete_locataire_without_active_contracts(): void
    {
        $locataire = Locataire::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('locataires.destroy', $locataire));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('locataires', [
            'id' => $locataire->id,
        ]);
    }

    public function test_prevent_deletion_of_locataire_with_active_contracts(): void
    {
        $locataire = Locataire::factory()->create();
        \App\Models\Contrat::factory()->create([
            'locataire_id' => $locataire->id,
            'statut' => 'actif'
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('locataires.destroy', $locataire));

        $response->assertStatus(409); // Conflict
        $this->assertDatabaseHas('locataires', ['id' => $locataire->id]);
    }

    public function test_gestionnaire_can_manage_locataires(): void
    {
        // Setup roles
        $this->artisan('app:setup-roles-permissions --force');
        $gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
        $gestionnaire->assignRole('gestionnaire');

        $response = $this->actingAs($gestionnaire)
            ->postJson(route('locataires.store'), [
                'nom' => 'Gestionnaire Tenant',
                'email' => 'gestionnaire@tenant.com',
                'telephone' => '770001111',
            ]);

        $response->assertStatus(201);
    }

    public function test_unauthorized_user_cannot_manage_locataires(): void
    {
        $comptable = User::factory()->create(['role' => 'comptable']);

        $response = $this->actingAs($comptable)
            ->postJson(route('locataires.store'), [
                'nom' => 'Secret Tenant',
                'email' => 'secret@example.com',
                'telephone' => '770000000',
            ]);

        $response->assertStatus(403);
    }
}
