<?php

namespace Tests\Feature;

use App\Models\Bien;
use App\Models\User;
use App\Models\Proprietaire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class GestionnaireBienTest extends TestCase
{
    use RefreshDatabase;

    private $gestionnaire;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles and permissions
        $this->artisan('app:setup-roles-permissions --force');

        // Create gestionnaire user
        $this->gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
        $this->gestionnaire->assignRole('gestionnaire');
    }

    public function test_gestionnaire_can_list_biens(): void
    {
        Bien::factory()->count(3)->create();

        $response = $this->actingAs($this->gestionnaire)
            ->get(route('biens.index'));

        $response->assertStatus(200);
    }

    public function test_gestionnaire_can_create_bien(): void
    {
        $proprietaire = Proprietaire::factory()->create();
        
        $response = $this->actingAs($this->gestionnaire)
            ->postJson(route('dashboard.biens.store'), [
                'nom' => 'Residence Gestionnaire',
                'adresse' => 'Rue de la Paix',
                'ville' => 'Dakar',
                'proprietaire_id' => $proprietaire->id,
                'loyer_mensuel' => 200000,
                'type' => 'appartement',
                'surface' => 120,
                'nombre_pieces' => 4,
                'meuble' => true,
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('biens', [
            'nom' => 'Residence Gestionnaire',
            'loyer_mensuel' => 200000,
        ]);
    }

    public function test_gestionnaire_can_update_bien(): void
    {
        $bien = Bien::factory()->create([
            'nom' => 'Original Name',
            'loyer_mensuel' => 100000,
        ]);

        $response = $this->actingAs($this->gestionnaire)
            ->putJson(route('dashboard.biens.update', $bien), [
                'nom' => 'Updated Name',
                'adresse' => $bien->adresse,
                'ville' => $bien->ville,
                'proprietaire_id' => $bien->proprietaire_id,
                'loyer_mensuel' => 110000,
                'type' => $bien->type,
                'surface' => $bien->surface,
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('biens', [
            'id' => $bien->id,
            'nom' => 'Updated Name',
            'loyer_mensuel' => 110000,
        ]);
    }

    public function test_gestionnaire_can_delete_bien_without_active_contracts(): void
    {
        $bien = Bien::factory()->create();

        $response = $this->actingAs($this->gestionnaire)
            ->deleteJson(route('dashboard.biens.delete', $bien));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('biens', [
            'id' => $bien->id,
        ]);
    }

    public function test_gestionnaire_can_delete_bien_image(): void
    {
        $bien = Bien::factory()->create();
        $image = \App\Models\BienImage::create([
            'bien_id' => $bien->id,
            'chemin' => 'test/image.jpg',
            'nom_original' => 'image.jpg',
            'principale' => true,
            'ordre' => 1,
        ]);

        $response = $this->actingAs($this->gestionnaire)
            ->deleteJson(route('dashboard.bien-images.delete', $image));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('bien_images', ['id' => $image->id]);
    }

    public function test_gestionnaire_cannot_create_payment(): void
    {
        $response = $this->actingAs($this->gestionnaire)
            ->postJson(route('paiements.store'), [
                'montant' => 1000,
            ]);

        $response->assertStatus(403);
    }
}
