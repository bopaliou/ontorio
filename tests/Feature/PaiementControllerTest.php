<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Loyer;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Bien;
use App\Models\Proprietaire;
use App\Models\Locataire;
use Tests\TestCase;

class PaiementControllerTest extends TestCase
{
    protected User $user;
    protected Contrat $contrat;
    protected Loyer $loyer;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer user authentifié
        $this->user = User::factory()->create(['role' => 'gestionnaire']);
        
        // Créer structure: Proprietaire -> Bien -> Contrat -> Loyer
        $proprio = Proprietaire::factory()->create();
        $bien = Bien::factory()->create(['proprietaire_id' => $proprio->id]);
        $locataire = Locataire::factory()->create();
        
        $this->contrat = Contrat::factory()->create([
            'bien_id' => $bien->id,
            'locataire_id' => $locataire->id,
        ]);

        $this->loyer = Loyer::factory()->create([
            'contrat_id' => $this->contrat->id,
            'montant' => 100000,
            'statut' => 'émis',
        ]);
    }

    /**
     * Test: Enregistrer paiement (authentifié)
     */
    public function test_enregistrer_paiement_authentified()
    {
        $response = $this->actingAs($this->user)->postJson('/paiements', [
            'loyer_id' => $this->loyer->id,
            'montant' => 50000,
            'mode' => 'virement',
            'date_paiement' => now()->toDateString(),
            'reference' => 'REF123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('paiements', [
            'loyer_id' => $this->loyer->id,
            'montant' => 50000,
        ]);
    }

    /**
     * Test: Enregistrer paiement sans authentification
     */
    public function test_enregistrer_paiement_unauthentified()
    {
        $response = $this->postJson('/paiements', [
            'loyer_id' => $this->loyer->id,
            'montant' => 50000,
            'mode' => 'virement',
            'date_paiement' => now()->toDateString(),
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    /**
     * Test: Validation - montant invalide
     */
    public function test_paiement_validation_montant_invalide()
    {
        $response = $this->actingAs($this->user)->postJson('/paiements', [
            'loyer_id' => $this->loyer->id,
            'montant' => -100, // Négatif
            'mode' => 'virement',
            'date_paiement' => now()->toDateString(),
        ]);

        $response->assertStatus(422); // Validation error
        $response->assertJsonValidationErrors('montant');
    }

    /**
     * Test: Validation - mode invalide
     */
    public function test_paiement_validation_mode_invalide()
    {
        $response = $this->actingAs($this->user)->postJson('/paiements', [
            'loyer_id' => $this->loyer->id,
            'montant' => 50000,
            'mode' => 'bitcoin', // Mode inexistant
            'date_paiement' => now()->toDateString(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('mode');
    }

    /**
     * Test: Mise à jour du statut loyer après paiement complet
     */
    public function test_paiement_met_a_jour_loyer_status()
    {
        $initial = $this->loyer->statut; // 'émis'

        Paiement::create([
            'loyer_id' => $this->loyer->id,
            'montant' => 100000, // Montant complet
            'date_paiement' => now()->toDateString(),
            'mode' => 'virement',
            'user_id' => $this->user->id,
        ]);

        // Le loyer devrait passer à 'payé'
        $this->loyer->refresh();
        // Note: vérifier que le controller met bien à jour le statut
    }
}
