<?php

namespace Tests\Unit\Routes;

use PHPUnit\Framework\TestCase;

class WebRoutesRegressionTest extends TestCase
{
    public function test_web_routes_do_not_use_undefined_profile_path_variable(): void
    {
        $routesPath = dirname(__DIR__, 3).'/routes/web.php';
        $contents = file_get_contents($routesPath);

        $this->assertNotFalse($contents);
        $this->assertStringNotContainsString('$profilePath', $contents);
        $this->assertStringContainsString("Route::get('/profile'", $contents);
        $this->assertStringContainsString("Route::patch('/profile'", $contents);
        $this->assertStringContainsString("Route::delete('/profile'", $contents);
    }
}
