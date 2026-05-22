<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define modules and their permissions
        $modules = [
            'dashboard' => ['view'],
            'clients' => ['view', 'create', 'edit', 'delete'],
            'products' => ['view', 'create', 'edit', 'delete'],
            'suppliers' => ['view', 'create', 'edit', 'delete'],
            'categories' => ['view', 'create', 'edit', 'delete'],
            'quotations' => ['view', 'create', 'edit', 'delete', 'send', 'approve'],
            'invoices' => ['view', 'create', 'edit', 'delete', 'send', 'cancel'],
            'payments' => ['view', 'create', 'edit', 'delete'],
            'receipts' => ['view', 'create', 'delete'],
            'delivery_notes' => ['view', 'create', 'edit', 'delete'],
            'income' => ['view', 'create', 'edit', 'delete'],
            'expenses' => ['view', 'create', 'edit', 'delete'],
            'reports' => ['view', 'export'],
            'settings' => ['view', 'edit'],
            'users' => ['view', 'create', 'edit', 'delete'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'templates' => ['view', 'create', 'edit', 'delete'],
            'subscription' => ['view', 'manage'],
        ];

        // Create permissions
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Create default roles with permissions
        $roles = [
            'owner' => Permission::all()->pluck('name')->toArray(),
            'admin' => [
                'dashboard.view',
                'clients.*',
                'products.*',
                'suppliers.*',
                'categories.*',
                'quotations.*',
                'invoices.*',
                'payments.*',
                'receipts.*',
                'delivery_notes.*',
                'income.*',
                'expenses.*',
                'reports.*',
                'settings.view',
                'users.view',
                'users.create',
                'users.edit',
            ],
            'finance' => [
                'dashboard.view',
                'clients.view',
                'products.view',
                'invoices.*',
                'payments.*',
                'receipts.*',
                'income.*',
                'expenses.*',
                'reports.*',
            ],
            'sales' => [
                'dashboard.view',
                'clients.*',
                'products.view',
                'quotations.*',
                'invoices.view',
                'invoices.create',
                'invoices.send',
            ],
            'warehouse' => [
                'dashboard.view',
                'products.*',
                'suppliers.view',
                'delivery_notes.*',
                'invoices.view',
            ],
            'viewer' => [
                'dashboard.view',
                'clients.view',
                'products.view',
                'suppliers.view',
                'quotations.view',
                'invoices.view',
                'payments.view',
                'receipts.view',
                'delivery_notes.view',
                'reports.view',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            // Handle wildcard permissions
            $resolvedPermissions = [];
            foreach ($permissions as $perm) {
                if (str_ends_with($perm, '.*')) {
                    $module = str_replace('.*', '', $perm);
                    $modulePerms = Permission::where('name', 'like', "{$module}.%")->pluck('name')->toArray();
                    $resolvedPermissions = array_merge($resolvedPermissions, $modulePerms);
                } else {
                    $resolvedPermissions[] = $perm;
                }
            }

            $role->syncPermissions(array_unique($resolvedPermissions));
        }
    }
}
