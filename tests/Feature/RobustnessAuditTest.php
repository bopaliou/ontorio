<?php

namespace Tests\Feature;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RobustnessAuditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Task 3.2: Prevent deletion of Bien with active contracts
     */
    public function test_prevent_deletion_of_bien_with_active_contracts()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $bien = Bien::factory()->create();
        $locataire = Locataire::factory()->create();
        
        // Create an active contract
        Contrat::factory()->create([
            'bien_id' => $bien->id,
            'locataire_id' => $locataire->id,
            'statut' => 'actif'
        ]);

        $this->actingAs($admin)
            ->deleteJson('/dashboard/biens/'.$bien->id)
            ->assertStatus(409)
            ->assertJsonPath('success', false);
            
        $this->assertDatabaseHas('biens', ['id' => $bien->id]);
    }

    /**
     * Test Task 3.3: Soft Deletes are working
     */
    public function test_soft_deletes_are_functional()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $bien = Bien::factory()->create();

        $this->actingAs($admin)
            ->deleteJson('/dashboard/biens/'.$bien->id)
            ->assertStatus(200);

        $this->assertSoftDeleted('biens', ['id' => $bien->id]);
    }

    /**
     * Test Task 3.1: Sequential payments work via service layer
     */
    public function test_paiement_service_validates_amounts()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $contrat = Contrat::factory()->create();
        $loyer = \App\Models\Loyer::factory()->create([
            'contrat_id' => $contrat->id,
            'montant' => 100000,
            'statut' => 'émis'
        ]);

        // Attempting to pay more than due (100k)
        $this->actingAs($admin)
            ->postJson('/paiements', [
                'loyer_id' => $loyer->id,
                'montant' => 150000,
                'mode' => 'espèces',
                'date_paiement' => now()->toDateString()
            ])
            ->assertStatus(422)
            ->assertJsonFragment(['success' => false])
            ->assertJsonPath('errors.montant.0', 'Le montant (150000) excède le reste à payer (100 000 F).');
    }
}
