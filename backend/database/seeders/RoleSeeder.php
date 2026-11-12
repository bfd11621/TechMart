<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Define Granular Permissions Grouped by Module
        $permissionDefinitions = [
            'dashboard' => [
                'dashboard.view' => 'View Executive Dashboard & High-Level KPIs',
            ],
            'users' => [
                'users.view' => 'View Administrative Staff Directory',
                'users.create' => 'Create New Staff Accounts',
                'users.update' => 'Modify Staff Profiles and Roles',
                'users.delete' => 'Delete Staff Accounts',
                'users.status' => 'Activate or Deactivate Staff Access',
            ],
            'roles' => [
                'roles.view' => 'View Roles and Capabilities',
                'roles.create' => 'Create Custom Roles',
                'roles.update' => 'Modify Role Details',
                'roles.delete' => 'Delete Custom Roles',
                'roles.permissions' => 'Assign and Synchronize Granular Permissions',
            ],
            'stores' => [
                'stores.view' => 'View Branch Stores and Warehouses',
                'stores.create' => 'Establish New Branch Store',
                'stores.update' => 'Modify Branch Store Information',
                'stores.delete' => 'Remove Branch Stores',
            ],
            'products' => [
                'products.view' => 'Browse and Search Hardware Products',
                'products.create' => 'Add New Products and Specification Groups',
                'products.update' => 'Modify Product Pricing, Specs, and Datasheets',
                'products.delete' => 'Remove Products from Catalog',
            ],
            'categories' => [
                'categories.view' => 'View Category Hierarchies',
                'categories.create' => 'Create Product Categories',
                'categories.update' => 'Modify Categories',
                'categories.delete' => 'Delete Categories',
            ],
            'brands' => [
                'brands.view' => 'View Manufacturer Brands',
                'brands.create' => 'Register Partner Brands',
                'brands.update' => 'Modify Brand Profiles',
                'brands.delete' => 'Delete Brands',
            ],
            'inventory' => [
                'inventory.view' => 'Inspect Store-Specific Stock Balances',
                'inventory.receive' => 'Receive Incoming Inventory Shipments',
                'inventory.issue' => 'Issue Stock for Dispatch or Maintenance',
                'inventory.adjust' => 'Perform Authorized Stock Adjustments with Reason',
            ],
            'stock_transfers' => [
                'stock_transfers.view' => 'View Inter-Store Stock Transfer Ledgers',
                'stock_transfers.create' => 'Initiate Inter-Store Transfers',
                'stock_transfers.approve' => 'Approve and Receive In-Transit Stock Transfers',
            ],
            'quotations' => [
                'quotations.view' => 'View RFQ Requests and Formal Quotations',
                'quotations.create' => 'Draft New Formal Quotations',
                'quotations.update' => 'Modify Quotation Line Items, Discounts, and Taxes',
                'quotations.approve' => 'Approve or Reject Client Quotations',
                'quotations.pdf' => 'Generate and Download Branded PDF Quotations',
            ],
            'sales' => [
                'sales.view' => 'View Confirmed Sales Orders and Ledgers',
                'sales.create' => 'Register Direct Walk-In or Corporate Sales',
                'sales.confirm' => 'Confirm Orders and Recognize Sales Revenue',
                'sales.payments' => 'Record Full and Partial Payment Receipts',
                'sales.invoice' => 'Generate Commercial Invoices and Delivery Slips',
            ],
            'analytics' => [
                'analytics.view' => 'Inspect Interactive Charts and BI Metrics',
            ],
            'reports' => [
                'reports.view' => 'Access Store and Consolidated Sales Reports',
                'reports.export' => 'Export Financial and Stock Reports to PDF/CSV',
            ],
            'settings' => [
                'settings.view' => 'View System & Website Content Settings',
                'settings.update' => 'Modify Corporate Settings and Branding',
            ],
        ];

        $createdPermissions = [];
        foreach ($permissionDefinitions as $module => $perms) {
            foreach ($perms as $slug => $description) {
                $name = ucwords(str_replace(['.', '_'], ' ', $slug));
                $permission = Permission::firstOrCreate(['slug' => $slug], [
                    'name' => $name,
                    'module' => $module,
                    'description' => $description,
                ]);
                $createdPermissions[$slug] = $permission->id;
            }
        }

        // 2. Define Initial Roles
        $rolesData = [
            RoleEnum::SUPER_ADMIN->value => [
                'name' => 'Super Administrator',
                'description' => 'Full administrative sovereignty over all branches, financial records, users, and system parameters.',
                'is_system' => true,
                'permissions' => array_keys($createdPermissions), // All permissions
            ],
            RoleEnum::STORE_MANAGER->value => [
                'name' => 'Store Manager',
                'description' => 'Autonomous administration of assigned store inventory, local stock movements, orders, and branch sales reports.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view',
                    'stores.view',
                    'products.view',
                    'categories.view',
                    'brands.view',
                    'inventory.view', 'inventory.receive', 'inventory.issue', 'inventory.adjust',
                    'stock_transfers.view', 'stock_transfers.create', 'stock_transfers.approve',
                    'quotations.view', 'quotations.create', 'quotations.pdf',
                    'sales.view', 'sales.create', 'sales.confirm', 'sales.payments', 'sales.invoice',
                    'analytics.view',
                    'reports.view', 'reports.export',
                ],
            ],
            RoleEnum::SALES_MANAGER->value => [
                'name' => 'Sales Manager',
                'description' => 'Customer RFQ qualification, formal quotation drafting, client negotiation, sales order registration, and payment tracking.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view',
                    'stores.view',
                    'products.view',
                    'categories.view',
                    'brands.view',
                    'inventory.view',
                    'quotations.view', 'quotations.create', 'quotations.update', 'quotations.approve', 'quotations.pdf',
                    'sales.view', 'sales.create', 'sales.confirm', 'sales.payments', 'sales.invoice',
                    'analytics.view',
                    'reports.view',
                ],
            ],
            RoleEnum::INVENTORY_OFFICER->value => [
                'name' => 'Inventory Officer',
                'description' => 'Warehouse stock receipts, issuing, stock count adjustments, shelf management, and inter-store transit execution.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view',
                    'stores.view',
                    'products.view',
                    'inventory.view', 'inventory.receive', 'inventory.issue', 'inventory.adjust',
                    'stock_transfers.view', 'stock_transfers.create', 'stock_transfers.approve',
                ],
            ],
            RoleEnum::GENERAL_ADMIN->value => [
                'name' => 'General Administrator',
                'description' => 'Product catalogue maintenance, category organization, brand management, customer showroom content, and general reporting.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view',
                    'stores.view',
                    'products.view', 'products.create', 'products.update',
                    'categories.view', 'categories.create', 'categories.update',
                    'brands.view', 'brands.create', 'brands.update',
                    'inventory.view',
                    'quotations.view',
                    'sales.view',
                    'analytics.view',
                    'reports.view',
                    'settings.view', 'settings.update',
                ],
            ],
        ];

        foreach ($rolesData as $slug => $data) {
            $role = Role::firstOrCreate(['slug' => $slug], [
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'],
                'is_system' => $data['is_system'],
            ]);

            // Sync role permissions
            $permissionIds = [];
            foreach ($data['permissions'] as $pSlug) {
                if (isset($createdPermissions[$pSlug])) {
                    $permissionIds[] = $createdPermissions[$pSlug];
                }
            }
            $role->permissions()->sync($permissionIds);
        }
    }
}
