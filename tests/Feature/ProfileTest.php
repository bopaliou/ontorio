<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
    }

    public function test_password_can_be_updated()
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $response = $this->actingAs($user)->put('/password', [
            'current_password' => 'password',
            'password' => 'Pa$$word123!',
            'password_confirmation' => 'Pa$$word123!',
        ]);

        if (session('errors')) {
            dump(session('errors')->getBag('updatePassword')->all());
            dump(session('errors')->all());
        }

        $response->assertSessionHasNoErrorsIn('updatePassword');
        $response->assertRedirect('/profile');

        $this->assertTrue(Hash::check('Pa$$word123!', $user->refresh()->password));
    }

    public function test_delete_account_section_visibility()
    {
        // 1. Non-admin (Gestionnaire) -> Should NOT see delete
        $user = User::factory()->create(['role' => 'gestionnaire']);
        $response = $this->actingAs($user)->get('/profile');
        $response->assertDontSee('Delete Account');
        $response->assertDontSee('Supprimer le compte');

        // 2. Admin -> Should SEE delete
        $admin = User::factory()->create(['role' => 'admin']);
        $responseAdmin = $this->actingAs($admin)->get('/profile');
        
        // Check for the text or ID. The blade has @include('...delete-user-form')
        // We should check for content inside the form, e.g., 'Delete Account' button or header.
        // In delete-user-form.blade.php: <h2>{{ __('Delete Account') }}</h2>
        // So we expect to see 'Delete Account' (or the french translation if locale is set, but test env is usually en unless set)
        // Wait, app locale might be fr?
        // Let's check both or verify what view returns.
        // I'll check for 'profile.partials.delete-user-form' hint if possible, or just text.
        // I'll check for "Delete Account" since I haven't translated the key likely in tests? 
        // Logic: if tranlsation file exists, it will use it.
        // I translated it in Step 327? No, I translated `edit.blade.php`, `update-profile`, `update-password`.
        // Did I translate `delete-user-form`?
        // I viewed `delete-user-form.blade.php` in Step 321 but didn't modify it. So it's still English "Delete Account"?
        // Wait, previous session summary said "Removed the 'Delete Account' functionality... reserving it for administrators."
        // And "Translated profile-related pages (Profile Info, Password Update)...".
        // It didn't explicitly say "Translated Delete Account form".
        // So it should be English "Delete Account".
        
        $responseAdmin->assertSee('Delete Account');
    }
}
