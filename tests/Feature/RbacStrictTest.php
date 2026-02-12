<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RbacStrictTest extends TestCase
{
    use RefreshDatabase;
    // For this environment where we just seeded, we can relying on the seeded users OR create new ones in transaction.
    // Given the environment constraints, let's try to checking against the known seeded users first.

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist
        if (Role::count() == 0) {
            $this->artisan('app:setup-roles-permissions --force');
        }

        // Ensure users exist
        $this->seed(\Database\Seeders\TestUsersSeeder::class);
    }

    public function test_direction_cannot_create_bien()
    {
        $user = User::where('email', 'direction@test.com')->first();
        if (! $user) {
            $this->markTestSkipped('Direction user not found');
        }

        $response = $this->actingAs($user)->post(route('dashboard.biens.store'), [
            'nom' => 'Bien Test Interdit',
        ]);

        $response->assertStatus(403);
    }

    public function test_gestionnaire_can_access_create_bien()
    {
        $user = User::where('email', 'gestionnaire@test.com')->first();
        if (! $user) {
            $this->markTestSkipped('Gestionnaire user not found');
        }

        // We check if they are authorized to hit the route.
        // Validation error (422) means they passed authorization (403).
        $response = $this->actingAs($user)->post(route('dashboard.biens.store'), []);

        $this->assertNotEquals(403, $response->status());
    }

    public function test_gestionnaire_cannot_create_paiement()
    {
        $user = User::where('email', 'gestionnaire@test.com')->first();
        if (! $user) {
            $this->markTestSkipped('Gestionnaire user not found');
        }

        $response = $this->actingAs($user)->post(route('paiements.store'), []);

        $response->assertStatus(403);
    }

    public function test_comptable_can_access_create_paiement()
    {
        $user = User::where('email', 'comptable@test.com')->first();
        if (! $user) {
            $this->markTestSkipped('Comptable user not found');
        }

        $response = $this->actingAs($user)->post(route('paiements.store'), []);

        // 422 Unprocessable Permission => Authorized but invalid data
        // 302 Redirect => Authorized but invalid data (redirect back)
        $this->assertNotEquals(403, $response->status());
    }

    public function test_comptable_cannot_delete_bien()
    {
        $user = User::where('email', 'comptable@test.com')->first();
        if (! $user) {
            $this->markTestSkipped('Comptable user not found');
        }

        // Create a property to delete
        $proprietaire = \App\Models\Proprietaire::create([
            'nom' => 'Prop Test',
            'email' => 'prop'.rand(1000, 9999).'@test.com',
            'telephone' => '770000000',
            'adresse' => 'Dakar',
        ]);

        $bien = \App\Models\Bien::create([
            'proprietaire_id' => $proprietaire->id,
            'nom' => 'Bien Test Delete',
            'adresse' => 'Rue Test',
            'type' => 'appartement',
            'statut' => 'occupé',
            'loyer_mensuel' => 100000,
        ]);

        $response = $this->actingAs($user)->delete(route('dashboard.biens.delete', ['bien' => $bien->id]));

        $response->assertStatus(403);
    }

    public function test_proprietaire_cannot_access_internal_operations()
    {
        // Ensure role exists (setup command might have been cached/skipped in test env)
        if (\Spatie\Permission\Models\Role::where('name', 'proprietaire')->doesntExist()) {
            \Spatie\Permission\Models\Role::create(['name' => 'proprietaire', 'guard_name' => 'web']);
        }

        // Create a user with 'proprietaire' role (and update legacy column to avoid fallback bypass)
        $user = User::factory()->create(['role' => 'proprietaire']);
        $user->assignRole('proprietaire');

        // Try to create a Bien (Internal operation)
        $response = $this->actingAs($user)->post(route('dashboard.biens.store'), []);

        $response->assertStatus(403);
    }
}
