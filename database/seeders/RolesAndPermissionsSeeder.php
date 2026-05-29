<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * All permission groups and their actions.
     * Format: 'group' => ['action', 'action', ...]
     * Resulting permission names: 'group.action'
     */
    private array $permissionGroups = [
        'dashboard'      => ['view'],
        'public_pages'   => ['view', 'manage'],
        'clients'        => ['view', 'create', 'edit', 'delete', 'restore', 'export'],
        'service_types'  => ['view', 'create', 'edit', 'delete'],
        'job_requests'   => ['view', 'create', 'edit', 'delete', 'approve', 'reject'],
        'jobs'           => ['view', 'create', 'edit', 'delete', 'restore', 'assign', 'export'],
        'work_orders'    => ['view', 'create', 'edit', 'delete', 'complete'],
        'job_assignments'=> ['view', 'create', 'edit', 'delete'],
        'quotes'         => ['view', 'create', 'edit', 'delete', 'approve', 'reject', 'send', 'export'],
        'invoices'       => ['view', 'create', 'edit', 'delete', 'mark_paid', 'send', 'export'],
        'payments'       => ['view', 'create', 'edit', 'delete', 'refund', 'export'],
        'job_photos'     => ['view', 'upload', 'delete'],
        'job_reports'    => ['view', 'create', 'edit', 'delete', 'export'],
        'equipment'      => ['view', 'create', 'edit', 'delete', 'restore'],
        'documents'      => ['view', 'upload', 'delete', 'download'],
        'users'          => ['view', 'create', 'edit', 'delete', 'restore', 'impersonate'],
        'roles'          => ['view', 'create', 'edit', 'delete', 'assign'],
        'settings'       => ['view', 'edit'],
        'audit_logs'     => ['view', 'export'],
        'media'          => ['view', 'upload', 'delete'],
        'ai_tools'       => ['view', 'use'],
        'system_health'  => ['view'],
        'guided_tours'   => ['view', 'manage'],
    ];

    /**
     * Role definitions with their permissions.
     * '*' means all permissions.
     */
    private array $roles = [
        'Developer'          => '*',
        'Director'           => [
            'dashboard.view',
            'clients.*', 'service_types.*', 'job_requests.*', 'jobs.*',
            'work_orders.*', 'job_assignments.*', 'quotes.*', 'invoices.*',
            'payments.*', 'job_photos.*', 'job_reports.*', 'equipment.*',
            'documents.*', 'users.view', 'users.create', 'users.edit',
            'roles.view', 'settings.*', 'audit_logs.*',
            'media.*', 'ai_tools.*', 'system_health.view', 'guided_tours.view',
        ],
        'Operations Manager' => [
            'dashboard.view',
            'clients.view', 'clients.create', 'clients.edit',
            'service_types.view', 'service_types.create', 'service_types.edit',
            'job_requests.*', 'jobs.*', 'work_orders.*', 'job_assignments.*',
            'quotes.view', 'quotes.create', 'quotes.edit',
            'invoices.view', 'job_photos.*', 'job_reports.*',
            'equipment.*', 'documents.view', 'documents.upload',
            'users.view', 'audit_logs.view', 'guided_tours.view',
        ],
        'Admin Officer'      => [
            'dashboard.view',
            'clients.*', 'service_types.view', 'job_requests.view', 'job_requests.create',
            'jobs.view', 'quotes.*', 'invoices.*', 'payments.view',
            'documents.*', 'users.view', 'settings.view', 'guided_tours.view',
        ],
        'Finance Officer'    => [
            'dashboard.view',
            'clients.view', 'quotes.*', 'invoices.*', 'payments.*',
            'job_reports.view', 'job_reports.export',
            'audit_logs.view', 'documents.view', 'documents.download',
            'guided_tours.view',
        ],
        'Supervisor'         => [
            'dashboard.view',
            'job_requests.view', 'jobs.view', 'jobs.edit',
            'work_orders.*', 'job_assignments.*', 'job_photos.*',
            'job_reports.view', 'job_reports.create',
            'equipment.view', 'equipment.edit',
            'guided_tours.view',
        ],
        'Field Worker'       => [
            'dashboard.view',
            'jobs.view', 'work_orders.view', 'work_orders.complete',
            'job_photos.view', 'job_photos.upload',
            'guided_tours.view',
        ],
        'Driver'             => [
            'dashboard.view',
            'jobs.view', 'job_assignments.view',
            'equipment.view',
            'guided_tours.view',
        ],
        'Client'             => [
            'public_pages.view',
            'job_requests.create',
            'quotes.view',
            'invoices.view',
            'payments.view',
            'guided_tours.view',
        ],
        'Auditor'            => [
            'dashboard.view',
            'audit_logs.*',
            'clients.view', 'jobs.view', 'invoices.view',
            'payments.view', 'users.view', 'settings.view',
            'guided_tours.view',
        ],
    ];

    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions
        $allPermissions = [];

        foreach ($this->permissionGroups as $group => $actions) {
            foreach ($actions as $action) {
                $name = "{$group}.{$action}";
                $permission = Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
                $allPermissions[] = $permission;
            }
        }

        // Create roles and assign permissions
        foreach ($this->roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            if ($permissions === '*') {
                $role->syncPermissions($allPermissions);
            } else {
                // Expand wildcard group permissions like 'clients.*'
                $expanded = [];
                foreach ($permissions as $perm) {
                    if (str_ends_with($perm, '.*')) {
                        $group = str_replace('.*', '', $perm);
                        foreach ($this->permissionGroups[$group] ?? [] as $action) {
                            $expanded[] = "{$group}.{$action}";
                        }
                    } else {
                        $expanded[] = $perm;
                    }
                }
                $role->syncPermissions($expanded);
            }

            $this->command->line("  Role [{$roleName}] seeded with ".count($role->permissions).' permissions.');
        }

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
