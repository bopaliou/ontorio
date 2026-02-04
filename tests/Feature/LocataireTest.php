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

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_create_locataire()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('locataires.store'), [
                'nom' => 'Jean Dupont',
                'email' => 'jean.dupont@example.com',
                'telephone' => '771234567',
                'adresse' => 'Dakar, Plateau',
                'cni' => '1234567890123',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('locataires', [
            'nom' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
        ]);
    }

    public function test_locataire_validation_fails()
    {
        // Nom manquant et email invalide
        $response = $this->actingAs($this->admin)
            ->postJson(route('locataires.store'), [
                'nom' => '',
                'email' => 'not-an-email',
                'telephone' => '771234567',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nom', 'email']);
    }

    public function test_admin_can_update_locataire()
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

    public function test_admin_can_delete_locataire_without_active_contracts()
    {
        $locataire = Locataire::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('locataires.destroy', $locataire));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('locataires', ['id' => $locataire->id]);
    }

    public function test_unauthorized_user_cannot_manage_locataires()
    {
        // On utilise 'comptable' qui n'a pas accès à la ressource locataires (selon web.php)
        // Note: web.php dit: Route::middleware(['auth', 'role:admin|direction|gestionnaire'])->group(...) pour locataires.
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
