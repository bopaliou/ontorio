<?php

namespace Tests\Unit;

use App\Helpers\PermissionHelper;
use App\Models\User;
use Tests\TestCase;

class PermissionHelperTest extends TestCase
{
    public function test_admin_has_all_permissions()
    {
        $admin = User::factory()->make(['role' => 'admin']);
        $this->actingAs($admin);

        $this->assertTrue(PermissionHelper::can('users.view'));
        $this->assertTrue(PermissionHelper::can('random.permission'));
    }

    public function test_comptable_permissions()
    {
        $comptable = User::factory()->make(['role' => 'comptable']);
        $this->actingAs($comptable);

        $this->assertTrue(PermissionHelper::can('loyers.view'));
        $this->assertFalse(PermissionHelper::can('users.view')); // Should be FALSE
    }
}
