<?php

namespace Tests\Feature;

use App\Models\Bien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BienTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_bien(): void
    {
        $proprietaire = \App\Models\Proprietaire::factory()->create();
        $response = $this->actingAs($this->admin)
            ->postJson(route('dashboard.biens.store'), [
                'nom' => 'Immeuble Test',
                'adresse' => '123 Rue Test',
                'ville' => 'Dakar',
                'proprietaire_id' => $proprietaire->id,
                'loyer_mensuel' => 150000,
                'type' => 'appartement',
                'surface' => 100,
                'nombre_pieces' => 3,
                'meuble' => false,
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('biens', [
            'nom' => 'Immeuble Test',
            'loyer_mensuel' => 150000,
        ]);
    }

    public function test_store_bien_validation_fails(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('dashboard.biens.store'), [
                'nom' => '',
                'loyer_mensuel' => 'not-a-number',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nom', 'loyer_mensuel']);
    }

    public function test_admin_can_update_bien(): void
    {
        $bien = Bien::factory()->create([
            'nom' => 'Old Name',
            'loyer_mensuel' => 100000,
            'type' => 'studio',
        ]);

        $response = $this->actingAs($this->admin)
            ->putJson(route('dashboard.biens.update', $bien), [
                'nom' => 'New Name',
                'adresse' => 'New Address',
                'ville' => 'Dakar',
                'proprietaire_id' => $bien->proprietaire_id,
                'loyer_mensuel' => 120000,
                'type' => 'studio',
                'surface' => 50,
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('biens', [
            'id' => $bien->id,
            'nom' => 'New Name',
            'loyer_mensuel' => 120000,
        ]);
    }

    public function test_admin_can_delete_bien(): void
    {
        $bien = Bien::factory()->create([
            'nom' => 'To Delete',
            'loyer_mensuel' => 50000,
            'type' => 'magasin',
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('dashboard.biens.delete', $bien));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('biens', [
            'id' => $bien->id,
        ]);
    }

    public function test_unauthorized_user_cannot_manage_biens(): void
    {
        $user = User::factory()->create(['role' => 'comptable']);

        $response = $this->actingAs($user)
            ->postJson(route('dashboard.biens.store'), [
                'nom' => 'Hacker Building',
                'loyer_mensuel' => 100000,
                'type' => 'appartement',
            ]);

        $response->assertStatus(403);
    }
}
