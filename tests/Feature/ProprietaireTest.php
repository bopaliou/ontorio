<?php

namespace Tests\Feature;

use App\Models\Proprietaire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProprietaireTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_store_proprietaire_validation(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('proprietaires.store'), [
            'nom' => '', // Required
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nom', 'email']);
    }

    public function test_cannot_delete_proprietaire_with_biens(): void
    {
        $prop = Proprietaire::factory()->create();
        \App\Models\Bien::factory()->create(['proprietaire_id' => $prop->id]);

        $response = $this->actingAs($this->admin)->deleteJson(route('proprietaires.destroy', $prop));

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
            
        $this->assertDatabaseHas('proprietaires', ['id' => $prop->id]);
    }

    public function test_can_delete_proprietaire_without_biens(): void
    {
        $prop = Proprietaire::factory()->create();

        $response = $this->actingAs($this->admin)->deleteJson(route('proprietaires.destroy', $prop));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('proprietaires', ['id' => $prop->id]);
    }
}
