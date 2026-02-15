<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private const ROUTE_LOGIN = '/login';

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(self::ROUTE_LOGIN);

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post(self::ROUTE_LOGIN, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post(self::ROUTE_LOGIN, [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_login_validation_rules(): void
    {
        $response = $this->post(self::ROUTE_LOGIN, [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
        
        $response = $this->post(self::ROUTE_LOGIN, [
            'email' => 'not-an-email',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_login_throttling_after_multiple_attempts(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 6; $i++) {
            $this->post(self::ROUTE_LOGIN, [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post(self::ROUTE_LOGIN, [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        // Check for common throttling message keywords (English or French)
        $errorMessage = strtolower(session('errors')->first('email'));
        $this->assertTrue(
            str_contains($errorMessage, 'too many') || 
            str_contains($errorMessage, 'trop de tentatives'),
            "Expected throttling error message, but got: {$errorMessage}"
        );
    }
}
