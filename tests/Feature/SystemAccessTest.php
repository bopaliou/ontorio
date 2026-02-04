<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SystemAccessTest extends TestCase
{
    public function test_migration_route_is_forbidden_without_correct_token()
    {
        // Set a token config
        Config::set('deploy.token', 'REAL_SECRET');

        $response = $this->get('/system/migrate/WRONG_TOKEN');

        $response->assertStatus(403);
    }

    public function test_migration_route_works_with_valid_token()
    {
        // Mock token
        Config::set('deploy.token', 'TEST_SECRET_KEY');

        // Mock artisan simply to avoid real execution noise (though safe in sqlite memory)
        Artisan::shouldReceive('call')->with('migrate', ['--force' => true])->once();
        Artisan::shouldReceive('output')->andReturn('Migration fake output');
        Artisan::shouldReceive('call')->with('optimize:clear')->once();
        Artisan::shouldReceive('call')->with('view:cache')->once();

        $response = $this->get('/system/migrate/TEST_SECRET_KEY');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
