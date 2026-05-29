<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_developer_role_exists(): void
    {
        $this->assertNotNull(Role::findByName('Developer'));
    }

    public function test_all_ten_roles_exist(): void
    {
        $roles = [
            'Developer', 'Director', 'Operations Manager', 'Admin Officer',
            'Finance Officer', 'Supervisor', 'Field Worker', 'Driver', 'Client', 'Auditor',
        ];
        foreach ($roles as $role) {
            $this->assertNotNull(Role::findByName($role), "Role [{$role}] should exist.");
        }
    }

    public function test_developer_has_all_permissions(): void
    {
        $developer = Role::findByName('Developer');
        $allCount  = Permission::count();

        $this->assertEquals($allCount, $developer->permissions()->count());
    }

    public function test_field_worker_cannot_access_settings(): void
    {
        $fieldWorker = Role::findByName('Field Worker');
        $this->assertFalse($fieldWorker->hasPermissionTo('settings.edit'));
        $this->assertFalse($fieldWorker->hasPermissionTo('settings.view'));
    }

    public function test_auditor_can_view_audit_logs(): void
    {
        $auditor = Role::findByName('Auditor');
        $this->assertTrue($auditor->hasPermissionTo('audit_logs.view'));
    }

    public function test_client_cannot_view_dashboard(): void
    {
        $client = Role::findByName('Client');
        $this->assertFalse($client->hasPermissionTo('dashboard.view'));
    }

    public function test_developer_user_can_access_dashboard(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Developer');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertStatus(200);
    }

    public function test_non_developer_cannot_access_system_health_page(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Field Worker');

        $this->actingAs($user)
            ->get(route('dashboard.system-health'))
            ->assertStatus(403);
    }

    public function test_settings_page_requires_permission(): void
    {
        // Field Worker should not see settings
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Field Worker');

        $this->actingAs($user)
            ->get(route('dashboard.settings.index'))
            ->assertStatus(403);
    }

    public function test_admin_officer_can_access_settings(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Admin Officer');

        $this->actingAs($user)
            ->get(route('dashboard.settings.index'))
            ->assertStatus(200);
    }
}
