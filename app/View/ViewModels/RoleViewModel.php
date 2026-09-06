<?php

namespace App\View\ViewModels;

class RoleViewModel
{
    /**
     * Get all roles with their assigned permission matrix.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Unrestricted authority across all system modules, configurations, and administrative functions.',
                'users_count' => 2,
                'is_system' => true,
                'permissions' => [
                    'products' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'inventory' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'orders' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'offline_sales' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'customers' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'invoices' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'reports' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'content' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'users' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'roles' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'settings' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Inventory Manager',
                'slug' => 'inventory-manager',
                'description' => 'Oversees warehouse stocks, transfers, lot numbers, and stock movement auditing.',
                'users_count' => 3,
                'is_system' => false,
                'permissions' => [
                    'products' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'inventory' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'orders' => ['view' => true, 'create' => false, 'edit' => true, 'delete' => false],
                    'offline_sales' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'customers' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'invoices' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'reports' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'content' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'users' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'roles' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'settings' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                ],
            ],
            [
                'id' => 3,
                'name' => 'Sales User (POS / Retail)',
                'slug' => 'sales-user',
                'description' => 'Executes offline counter sales, customer lookups, order status reviews, and receipts.',
                'users_count' => 5,
                'is_system' => false,
                'permissions' => [
                    'products' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'inventory' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'orders' => ['view' => true, 'create' => true, 'edit' => false, 'delete' => false],
                    'offline_sales' => ['view' => true, 'create' => true, 'edit' => false, 'delete' => false],
                    'customers' => ['view' => true, 'create' => true, 'edit' => false, 'delete' => false],
                    'invoices' => ['view' => true, 'create' => true, 'edit' => false, 'delete' => false],
                    'reports' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'content' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'users' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'roles' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'settings' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                ],
            ],
            [
                'id' => 4,
                'name' => 'Content Manager',
                'slug' => 'content-manager',
                'description' => 'Manages wellness journal, brand story, clinic credentials, banners, and FAQs.',
                'users_count' => 2,
                'is_system' => false,
                'permissions' => [
                    'products' => ['view' => true, 'create' => false, 'edit' => true, 'delete' => false],
                    'inventory' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'orders' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'offline_sales' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'customers' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'invoices' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'reports' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'content' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'users' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'roles' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                    'settings' => ['view' => false, 'create' => false, 'edit' => false, 'delete' => false],
                ],
            ],
        ];
    }

    /**
     * Standard module list for matrix rendering.
     *
     * @return array<string, string>
     */
    public static function modules(): array
    {
        return [
            'products' => 'Products & Formulations',
            'inventory' => 'Inventory & Stock Movements',
            'orders' => 'Online Orders',
            'offline_sales' => 'Offline POS Sales',
            'customers' => 'Customer Management',
            'invoices' => 'Invoices & Receipts',
            'reports' => 'Financial & Stock Reports',
            'content' => 'Content & CMS Pages',
            'users' => 'Administrative Users',
            'roles' => 'Roles & Permissions',
            'settings' => 'System & Store Settings',
        ];
    }
}
