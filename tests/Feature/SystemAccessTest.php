<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SystemAccessTest extends TestCase
{
    public function test_migration_route_is_forbidden_without_correct_token()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        Config::set('deploy.token', 'REAL_SECRET');

        $response = $this->actingAs($admin)->post('/system/migrate', ['token' => 'WRONG_TOKEN']);

        $response->assertStatus(403);
    }

    public function test_migration_route_works_with_valid_token()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        Config::set('deploy.token', 'TEST_SECRET_KEY');

        Artisan::shouldReceive('call')->with('migrate', ['--force' => true])->once();
        Artisan::shouldReceive('output')->andReturn('Migration fake output');
        Artisan::shouldReceive('call')->with('cache:clear')->once();
        Artisan::shouldReceive('call')->with('view:clear')->once();
        Artisan::shouldReceive('call')->with('config:clear')->once();
        Artisan::shouldReceive('call')->with('route:clear')->once();
        Artisan::shouldReceive('call')->with('view:cache')->once();

        $response = $this->actingAs($admin)->post('/system/migrate', ['token' => 'TEST_SECRET_KEY']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
