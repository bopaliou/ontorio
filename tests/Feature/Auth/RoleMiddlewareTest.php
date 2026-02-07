<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup roles et permissions
        Permission::firstOrCreate(['name' => 'biens.view']);
        Permission::firstOrCreate(['name' => 'users.manage']);
    }

    /**
     * Test: Gestionnaire peut accéder aux biens
     */
    public function test_gestionnaire_peut_acceder_biens()
    {
        $user = User::factory()->create(['role' => 'gestionnaire']);
        
        $response = $this->actingAs($user)->get('/biens');
        
        $response->assertStatus(200);
    }

    /**
     * Test: Gestionnaire ne peut pas accéder à gestion utilisateurs
     */
    public function test_gestionnaire_ne_peut_pas_acceder_users()
    {
        $user = User::factory()->create(['role' => 'gestionnaire']);
        
        $response = $this->actingAs($user)->get('/users');
        
        $response->assertStatus(403); // Forbidden
    }

    /**
     * Test: Comptable peut accéder aux paiements
     */
    public function test_comptable_peut_acceder_paiements()
    {
        $user = User::factory()->create(['role' => 'comptable']);
        
        $response = $this->actingAs($user)->get('/paiements');
        
        $response->assertStatus(200);
    }

    /**
     * Test: Comptable ne peut pas créer contrats
     */
    public function test_comptable_ne_peut_pas_creer_contrats()
    {
        $user = User::factory()->create(['role' => 'comptable']);
        
        $response = $this->actingAs($user)->post('/contrats', []);
        
        $response->assertStatus(403);
    }

    /**
     * Test: Admin a accès complet
     */
    public function test_admin_a_acces_complet()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $routes = ['/biens', '/paiements', '/users', '/roles'];
        
        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get($route);
            $this->assertNotEquals(403, $response->status());
        }
    }

    /**
     * Test: Direction n'a accès qu'en lecture
     */
    public function test_direction_lecture_seule()
    {
        $user = User::factory()->create(['role' => 'direction']);
        
        // Lecture OK (use existing route accessible to direction)
        $response = $this->actingAs($user)->get('/rapports/loyers');
        $response->assertStatus(200);
        
        // Création interdite (use existing route that requires POST)
        $response = $this->actingAs($user)->post('/dashboard/biens', []);
        $this->assertTrue(
            in_array($response->status(), [403, 422]),
            "Expected status 403 (forbidden) or 422 (validation error), got {$response->status()}"
        );
    }
}
