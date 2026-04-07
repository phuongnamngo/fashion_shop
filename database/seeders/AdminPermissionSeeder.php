<?php

namespace Database\Seeders;

use App\Models\AdminFeature;
use App\Models\AdminRolePermission;
use Illuminate\Database\Seeder;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            ['feature_key' => 'dashboard', 'name' => 'Dashboard'],
            ['feature_key' => 'products', 'name' => 'Products'],
            ['feature_key' => 'categories', 'name' => 'Categories'],
            ['feature_key' => 'orders', 'name' => 'Orders'],
            ['feature_key' => 'payments', 'name' => 'Payments'],
            ['feature_key' => 'pages', 'name' => 'Static Pages'],
            ['feature_key' => 'contacts', 'name' => 'Contacts'],
            ['feature_key' => 'admins', 'name' => 'Admin Accounts'],
            ['feature_key' => 'permissions', 'name' => 'Role Permissions'],
        ];

        foreach ($features as $featureData) {
            $feature = AdminFeature::updateOrCreate(
                ['feature_key' => $featureData['feature_key']],
                $featureData
            );

            // Super admin: full access on every feature.
            AdminRolePermission::updateOrCreate(
                ['role' => 'super_admin', 'admin_feature_id' => $feature->id],
                [
                    'can_view' => true,
                    'can_create' => true,
                    'can_update' => true,
                    'can_delete' => true,
                ]
            );

            // Admin: limited access based on feature.
            $limitedAccess = match ($feature->feature_key) {
                'dashboard' => ['can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
                'products', 'categories', 'orders', 'pages' => ['can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
                'contacts' => ['can_view' => true, 'can_create' => false, 'can_update' => true, 'can_delete' => false],
                'payments' => ['can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
                'admins', 'permissions' => ['can_view' => false, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
                default => ['can_view' => false, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            };

            AdminRolePermission::updateOrCreate(
                ['role' => 'admin', 'admin_feature_id' => $feature->id],
                $limitedAccess
            );
        }
    }
}
