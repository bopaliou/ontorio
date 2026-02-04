<?php

namespace Tests\Feature;

use App\Models\Bien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BienTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur Admin pour les tests
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_create_bien()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('dashboard.biens.store'), [
                'nom' => 'Immeuble Test',
                'adresse' => '123 Rue Test',
                'loyer_mensuel' => 150000,
                'type' => 'appartement',
                'nombre_pieces' => 3,
                'meuble' => false,
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('biens', [
            'nom' => 'Immeuble Test',
            'loyer_mensuel' => 150000,
        ]);
    }

    public function test_store_bien_validation_fails()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('dashboard.biens.store'), [
                'nom' => '', // Validations: required
                'loyer_mensuel' => 'not-a-number', // Validations: numeric
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nom', 'loyer_mensuel']);
    }

    public function test_admin_can_update_bien()
    {
        // Création préalable (via le endpoint ou factory)
        $this->actingAs($this->admin)->postJson(route('dashboard.biens.store'), [
            'nom' => 'Old Name',
            'loyer_mensuel' => 100000,
            'type' => 'studio',
        ]);

        $bien = Bien::first();

        $response = $this->actingAs($this->admin)
            ->putJson(route('dashboard.biens.update', $bien), [
                'nom' => 'New Name',
                'adresse' => 'New Address',
                'loyer_mensuel' => 120000,
                'type' => 'studio',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('biens', [
            'id' => $bien->id,
            'nom' => 'New Name',
            'loyer_mensuel' => 120000,
        ]);
    }

    public function test_admin_can_delete_bien()
    {
        $this->actingAs($this->admin)->postJson(route('dashboard.biens.store'), [
            'nom' => 'To Delete',
            'loyer_mensuel' => 50000,
            'type' => 'magasin',
        ]);

        $bien = Bien::first();

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('dashboard.biens.delete', $bien));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('biens', ['id' => $bien->id]);
    }

    public function test_unauthorized_user_cannot_manage_biens()
    {
        $user = User::factory()->create(['role' => 'comptable']); // Rôle autorisé mais pas pour cette ressource

        $response = $this->actingAs($user)
            ->postJson(route('dashboard.biens.store'), [
                'nom' => 'Hacker Building',
                'loyer_mensuel' => 100000,
                'type' => 'appartement',
            ]);

        $response->assertStatus(403); // Middleware role check
    }
}
