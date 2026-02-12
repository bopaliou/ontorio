<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'gestionnaire',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Configure the factory to assign a Spatie role after creating the user.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            // Synchronisation automatique du rôle Spatie avec la colonne legacy 'role'
            // Utile pour la compatibilité des tests existants
            try {
                if (! empty($user->role)) {
                    // On vérifie si les rôles sont seedés avant d'assigner
                    if (\Spatie\Permission\Models\Role::where('name', $user->role)->exists()) {
                        $user->assignRole($user->role);
                    }
                }
            } catch (\Throwable $e) {
                // On échoue silencieusement pour ne pas bloquer les tests sans DB
            }
        });
    }
}
