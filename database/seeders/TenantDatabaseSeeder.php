<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Creating permissions...');
        $this->createPermissions();

        $this->command->info('Creating roles...');
        $this->createRoles();

        $this->command->info('Tenant database seeded successfully!');
    }

    /**
     * Create all permissions
     */
    private function createPermissions(): void
    {
        $permissions = [
            // User Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.manage-roles',

            // Role & Permission Management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.assign',

            // Company Settings
            'company.view-settings',
            'company.edit-settings',

            // Products
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            'products.manage-stock',
            'products.import',
            'products.export',

            // Categories
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',

            // Sales
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
            'sales.approve',
            'sales.void',

            // Customers
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',

            // Suppliers
            'suppliers.view',
            'suppliers.create',
            'suppliers.edit',
            'suppliers.delete',

            // Purchases
            'purchases.view',
            'purchases.create',
            'purchases.edit',
            'purchases.delete',
            'purchases.approve',

            // Inventory
            'inventory.view',
            'inventory.adjust',
            'inventory.transfer',

            // Reports
            'reports.view-sales',
            'reports.view-inventory',
            'reports.view-profit-loss',
            'reports.view-analytics',
            'reports.export',

            // Transactions
            'transactions.view',
            'transactions.create',
            'transactions.edit',
            'transactions.delete',

            // Dashboard
            'dashboard.view',
            'dashboard.view-analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

//        $this->command->info("   ✓ Created {count} permissions", ['count' => count($permissions)]);
    }

    /**
     * Create roles and assign permissions
     */
    private function createRoles(): void
    {
        // Admin Role - Full Access
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());
        $this->command->info('Admin role created with all permissions');

        // Manager Role - Limited Management Access
        $manager = Role::create(['name' => 'manager']);
        $managerPermissions = [
            // Users (view only)
            'users.view',

            // Products
            'products.view',
            'products.create',
            'products.edit',
            'products.manage-stock',
            'products.export',

            // Categories
            'categories.view',
            'categories.create',
            'categories.edit',

            // Sales
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.approve',

            // Customers
            'customers.view',
            'customers.create',
            'customers.edit',

            // Suppliers
            'suppliers.view',
            'suppliers.create',
            'suppliers.edit',

            // Purchases
            'purchases.view',
            'purchases.create',
            'purchases.edit',
            'purchases.approve',

            // Inventory
            'inventory.view',
            'inventory.adjust',
            'inventory.transfer',

            // Reports
            'reports.view-sales',
            'reports.view-inventory',
            'reports.view-profit-loss',
            'reports.view-analytics',
            'reports.export',

            // Transactions
            'transactions.view',
            'transactions.create',

            // Dashboard
            'dashboard.view',
            'dashboard.view-analytics',
        ];
        $manager->givePermissionTo($managerPermissions);
//        $this->command->info('Manager role created with ' . count($managerPermissions) . ' permissions');

        // Seller Role - Sales Focused
        $seller = Role::create(['name' => 'seller']);
        $sellerPermissions = [
            // Products (view only)
            'products.view',

            // Categories (view only)
            'categories.view',

            // Sales
            'sales.view',
            'sales.create',
            'sales.edit',

            // Customers
            'customers.view',
            'customers.create',
            'customers.edit',

            // Inventory (view only)
            'inventory.view',

            // Reports (limited)
            'reports.view-sales',

            // Dashboard
            'dashboard.view',
        ];
        $seller->givePermissionTo($sellerPermissions);
//        $this->command->info('Seller role created with ' . json_encode(count($sellerPermissions)) . ' permissions');
    }
}
