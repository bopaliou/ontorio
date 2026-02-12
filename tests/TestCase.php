<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // On s'assure que les rôles et permissions sont toujours chargés pour les tests
        // car beaucoup de tests s'appuient sur des accès par rôles maintenant.
        if (class_exists(\Spatie\Permission\Models\Role::class) && \Spatie\Permission\Models\Role::count() === 0) {
            \Illuminate\Support\Facades\Artisan::call('app:setup-roles-permissions');
        }
    }
}
