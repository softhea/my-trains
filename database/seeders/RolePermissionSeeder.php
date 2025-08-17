<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // User Management
            ['name' => 'users.view', 'display_name' => 'View Users', 'description' => 'View user list and details', 'group' => 'users'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'description' => 'Create new users', 'group' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'description' => 'Edit user information', 'group' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'description' => 'Delete users', 'group' => 'users'],
            ['name' => 'users.assign_roles', 'display_name' => 'Assign Roles', 'description' => 'Assign roles to users', 'group' => 'users'],
            
            // Product Management
            ['name' => 'products.view', 'display_name' => 'View Products', 'description' => 'View product list and details', 'group' => 'products'],
            ['name' => 'products.create', 'display_name' => 'Create Products', 'description' => 'Create new products', 'group' => 'products'],
            ['name' => 'products.edit', 'display_name' => 'Edit Products', 'description' => 'Edit product information', 'group' => 'products'],
            ['name' => 'products.delete', 'display_name' => 'Delete Products', 'description' => 'Delete products', 'group' => 'products'],
            ['name' => 'products.own', 'display_name' => 'Manage Own Products', 'description' => 'Manage own products', 'group' => 'products'],
            
            // Category Management
            ['name' => 'categories.view', 'display_name' => 'View Categories', 'description' => 'View category list and details', 'group' => 'categories'],
            ['name' => 'categories.create', 'display_name' => 'Create Categories', 'description' => 'Create new categories', 'group' => 'categories'],
            ['name' => 'categories.edit', 'display_name' => 'Edit Categories', 'description' => 'Edit category information', 'group' => 'categories'],
            ['name' => 'categories.delete', 'display_name' => 'Delete Categories', 'description' => 'Delete categories', 'group' => 'categories'],
            
            // Order Management
            ['name' => 'orders.view', 'display_name' => 'View Orders', 'description' => 'View order list and details', 'group' => 'orders'],
            ['name' => 'orders.create', 'display_name' => 'Create Orders', 'description' => 'Create new orders', 'group' => 'orders'],
            ['name' => 'orders.edit', 'display_name' => 'Edit Orders', 'description' => 'Edit order information', 'group' => 'orders'],
            ['name' => 'orders.delete', 'display_name' => 'Delete Orders', 'description' => 'Delete orders', 'group' => 'orders'],
            ['name' => 'orders.own', 'display_name' => 'Manage Own Orders', 'description' => 'Manage own orders', 'group' => 'orders'],
            
            // General Permissions
            ['name' => 'admin.access', 'display_name' => 'Access Admin Panel', 'description' => 'Access to admin panel', 'group' => 'admin'],
            ['name' => 'profile.edit', 'display_name' => 'Edit Own Profile', 'description' => 'Edit own profile information', 'group' => 'profile'],
        
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create Roles
        $roles = [
            [
                'id' => 1,
                'name' => 'superadmin',
                'display_name' => 'Super Administrator',
                'description' => 'Full system access and user management',
                'is_deletable' => false
            ],
            [
                'id' => 2,
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Product and order management access',
                'is_deletable' => true
            ],
            [
                'id' => 3,
                'name' => 'user',
                'display_name' => 'User',
                'description' => 'Regular user with basic access',
                'is_deletable' => false
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Assign permissions to roles
        $superadmin = Role::find(1);
        $admin = Role::find(2);
        $user = Role::find(3);

        // SuperAdmin gets all permissions
        $allPermissions = Permission::all();
        $superadmin->permissions()->attach($allPermissions);

        // Admin gets product, category, and order management permissions
        $adminPermissions = Permission::whereIn('name', [
            // 'admin.access',
            'products.view', 'products.create', 'products.edit', 'products.delete', 'products.own',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'orders.view', 'orders.create', 'orders.edit', 'orders.delete', 'orders.own',
            'profile.edit'
        ])->get();
        $admin->permissions()->attach($adminPermissions);

        // Regular user gets basic permissions
        $userPermissions = Permission::whereIn('name', [
            'profile.edit',
            'products.view', 'products.create', 'products.own',
            'orders.create', 'orders.own'
        ])->get();
        $user->permissions()->attach($userPermissions);
    }
}
