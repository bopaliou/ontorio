<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    private const MIGRATE_URI = '/system/migrate';

    use RefreshDatabase;

    /**
     * Test Task 1.1: Route /system/migrate/ is protected and requires secret
     */
    public function test_system_migrate_is_protected_and_requires_valid_secret()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $secret = 'test_secret_123';
        Config::set('deploy.token', $secret);
        Config::set('deploy.allow_web_migrate', true);

        // 1. Forbidden for Guest
        $this->post(self::MIGRATE_URI, ['token' => $secret])->assertRedirect('/login');

        // 2. Forbidden for Non-admin
        $gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
        $this->actingAs($gestionnaire)->post(self::MIGRATE_URI, ['token' => $secret])->assertStatus(403);

        // 3. Valid secret for Admin
        \Illuminate\Support\Facades\Artisan::shouldReceive('call')->with('migrate', ['--force' => true])->once();
        \Illuminate\Support\Facades\Artisan::shouldReceive('output')->andReturn('Migration fake output');
        \Illuminate\Support\Facades\Artisan::shouldReceive('call')->with('cache:clear')->once();
        \Illuminate\Support\Facades\Artisan::shouldReceive('call')->with('view:clear')->once();
        \Illuminate\Support\Facades\Artisan::shouldReceive('call')->with('config:clear')->once();
        \Illuminate\Support\Facades\Artisan::shouldReceive('call')->with('route:clear')->once();
        \Illuminate\Support\Facades\Artisan::shouldReceive('call')->with('view:cache')->once();

        $this->actingAs($admin)->post(self::MIGRATE_URI, ['token' => $secret])->assertStatus(200);

        // 4. Invalid secret for Admin
        $this->actingAs($admin)->post(self::MIGRATE_URI, ['token' => 'wrong_secret'])->assertStatus(403);
    }

    /**
     * Test Task 1.2: Filter role on sensitive APIs
     */
    public function test_api_stats_restricted_to_authorized_roles()
    {
        $authorizedRoles = ['admin', 'direction', 'gestionnaire'];
        $unauthorizedRoles = ['comptable'];

        foreach ($authorizedRoles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get('/api/stats/kpis')->assertStatus(200);
        }

        foreach ($unauthorizedRoles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get('/api/stats/kpis')->assertStatus(403);
        }
    }

    /**
     * Test Task 1.3: Rate Limiting
     * We use a smaller loop and mock the limiter if necessary,
     * but here we just ensure we hit a throttled route.
     */
    public function test_rate_limiting_on_sensitive_routes()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        // Clear limiter before starting
        \Illuminate\Support\Facades\RateLimiter::clear('global-mutations:'.$user->id);

        for ($i = 0; $i < 50; $i++) {
            $response = $this->post('/paiements', []);
            if ($response->status() === 429) {
                break;
            } // Already hit?
        }

        $this->post('/paiements', [])->assertStatus(429);
    }
}
