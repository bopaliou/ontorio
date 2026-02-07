<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Seed roles and permissions for tests so authorization checks pass
        if ($app->environment('testing')) {
            try {
                $app->make(Kernel::class)->call('db:seed', ['--class' => \Database\Seeders\RolesAndPermissionsSeeder::class, '--quiet' => true]);
            } catch (\Exception $e) {
                // If seeding fails, don't block tests here — tests will report missing roles.
            }
        }

        return $app;
    }
}
