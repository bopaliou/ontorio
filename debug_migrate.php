<?php

require __DIR__.'/vendor/autoload.php';

putenv('APP_ENV=testing');
putenv('CACHE_STORE=array');

try {
    $app = require_once __DIR__.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo 'App bootstrapped...'.PHP_EOL;

    // Configure in-memory database
    $config = $app['config'];
    $config->set('database.default', 'sqlite');
    $config->set('database.connections.sqlite.database', ':memory:');

    // Create connection
    $db = $app['db'];
    $connection = $db->connection('sqlite');
    $connection->getPdo();
    echo 'Database connection established...'.PHP_EOL;

    // Run migrations
    echo 'Running migrate:fresh...'.PHP_EOL;
    $kernel->call('migrate:fresh', [
        '--database' => 'sqlite',
        '--path' => 'database/migrations',
        '--realpath' => true,
    ]);

    echo 'Migrations completed successfully.'.PHP_EOL;

} catch (\Throwable $e) {
    file_put_contents('migrate_error.txt', 'ERROR: '.$e->getMessage().PHP_EOL.$e->getTraceAsString());
    echo 'ERROR logged to migrate_error.txt'.PHP_EOL;
}
