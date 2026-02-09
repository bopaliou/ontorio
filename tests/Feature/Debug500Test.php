<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Debug500Test extends TestCase
{
    // use RefreshDatabase;

    public function test_basic_route(): void
    {
        file_put_contents('debug_trace_basic.txt', "Step 1: Start basic\n");
        $this->withoutExceptionHandling();
        $response = $this->get('/test-debug');
        file_put_contents('debug_trace_basic.txt', "Step 2: Status " . $response->status() . "\n", FILE_APPEND);
        $response->assertStatus(200);
    }

    public function test_auth_route(): void
    {
        file_put_contents('debug_trace_auth.txt', "Step 1: Start auth test\n");
        $this->withoutExceptionHandling();
        
        try {
            $user = User::create([
                'name' => 'Test Auth',
                'email' => 'auth@example.com',
                'password' => 'password',
                'role' => 'admin',
            ]);
            file_put_contents('debug_trace_auth.txt', "Step 2: User create success. ID: " . $user->id . "\n", FILE_APPEND);
        } catch (\Throwable $e) {
            file_put_contents('debug_trace_auth.txt', "Step 2: User create FAILED: " . $e->getMessage() . "\n", FILE_APPEND);
            throw $e;
        }

        try {
            $this->actingAs($user);
            file_put_contents('debug_trace_auth.txt', "Step 3: actingAs success\n", FILE_APPEND);
            
            $response = $this->get('/test-debug');
            file_put_contents('debug_trace_auth.txt', "Step 4: Request success. Status: " . $response->status() . "\n", FILE_APPEND);
            
            $response->assertStatus(200);
            file_put_contents('debug_trace_auth.txt', "Step 5: Assertion success\n", FILE_APPEND);
        } catch (\Throwable $e) {
            file_put_contents('debug_trace_auth.txt', "ERROR after auth: " . $e->getMessage() . "\n", FILE_APPEND);
            throw $e;
        }
    }
    
    public function test_auth_middleware_route(): void
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }
}
