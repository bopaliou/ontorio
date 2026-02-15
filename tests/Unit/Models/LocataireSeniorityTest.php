<?php

namespace Tests\Unit\Models;

use App\Models\Locataire;
use App\Models\Contrat;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocataireSeniorityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_calculates_seniority_in_months_correctly()
    {
        // On crée un locataire
        $locataire = Locataire::factory()->create();

        // On lui crée un premier contrat datant d'il y a 5 mois
        Contrat::factory()->create([
            'locataire_id' => $locataire->id,
            'date_debut' => Carbon::now()->subMonths(5)->startOfMonth()
        ]);

        // L'ancienneté attendue est de 5 mois
        $this->assertEquals(5, $locataire->anciennete_mois);
    }

    /** @test */
    public function it_returns_zero_seniority_if_no_contracts()
    {
        $locataire = Locataire::factory()->create();
        $this->assertEquals(0, $locataire->anciennete_mois);
    }
}
