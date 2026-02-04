<?php

namespace Tests\Feature;

use App\Models\Loyer;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaiementTest extends TestCase
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

    public function test_recording_full_payment_completes_loyer()
    {
        $loyer = Loyer::factory()->create([
            'montant' => 100000,
            'statut' => 'émis',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('paiements.store'), [
                'loyer_id' => $loyer->id,
                'montant' => 100000,
                'date_paiement' => now()->format('Y-m-d'),
                'mode' => 'espèces',
            ]);

        // Note: Controller returns a script tag for iframe response
        $response->assertStatus(200)
            ->assertSee('Paiement enregistré avec succès');

        $this->assertDatabaseHas('paiements', [
            'loyer_id' => $loyer->id,
            'montant' => 100000,
        ]);

        $this->assertEquals('payé', $loyer->fresh()->statut);
    }

    public function test_recording_partial_payment_marks_loyer_as_partiel()
    {
        $loyer = Loyer::factory()->create([
            'montant' => 100000,
            'statut' => 'émis',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('paiements.store'), [
                'loyer_id' => $loyer->id,
                'montant' => 40000,
                'date_paiement' => now()->format('Y-m-d'),
                'mode' => 'mobile_money',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('partiellement_payé', $loyer->fresh()->statut);
    }

    public function test_payment_with_proof_upload()
    {
        Storage::fake('public');
        $loyer = Loyer::factory()->create();
        $file = UploadedFile::fake()->image('preuve.jpg');

        $response = $this->actingAs($this->admin)
            ->post(route('paiements.store'), [
                'loyer_id' => $loyer->id,
                'montant' => $loyer->montant,
                'date_paiement' => now()->format('Y-m-d'),
                'preuve' => $file,
            ]);

        $response->assertStatus(200);

        $paiement = Paiement::first();
        $this->assertNotNull($paiement->preuve);
        Storage::disk('public')->assertExists($paiement->preuve);
    }

    public function test_deleting_payment_restores_loyer_status()
    {
        $loyer = Loyer::factory()->create([
            'montant' => 100000,
            'statut' => 'payé',
            'mois' => now()->subMonth()->format('Y-m-01'),
        ]);

        $paiement = Paiement::factory()->create([
            'loyer_id' => $loyer->id,
            'montant' => 100000,
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('paiements.destroy', $paiement));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Puisque c'est un mois passé, le statut doit être 'en_retard'
        $this->assertEquals('en_retard', $loyer->fresh()->statut);
    }
}
