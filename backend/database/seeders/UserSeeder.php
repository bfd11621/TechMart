<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('slug', RoleEnum::SUPER_ADMIN->value)->first();
        $storeManagerRole = Role::where('slug', RoleEnum::STORE_MANAGER->value)->first();
        $salesManagerRole = Role::where('slug', RoleEnum::SALES_MANAGER->value)->first();
        $inventoryOfficerRole = Role::where('slug', RoleEnum::INVENTORY_OFFICER->value)->first();

        $hqStore = Store::where('is_primary', true)->first();
        $downtownStore = Store::where('code', 'ST-DOWNTOWN-02')->first();

        // 1. Super Admin (Dagim Wondim)
        User::updateOrCreate(
            ['email' => 'dagimw@iracktech.com'],
            [
                'name' => 'Dagim Wondim',
                'password' => Hash::make('Admin@iRACK2026!'),
                'phone' => '+251946428560',
                'role_id' => $superAdminRole->id,
                'store_id' => null, // Global access
                'is_active' => true,
            ]
        );

        // Also alias / support admin@irack.com
        User::firstOrCreate(
            ['email' => 'admin@irack.com'],
            [
                'name' => 'Dagim Wondim',
                'password' => Hash::make('Admin@iRACK2026!'),
                'phone' => '+251946428560',
                'role_id' => $superAdminRole->id,
                'store_id' => null, // Global access
                'is_active' => true,
            ]
        );

        // 2. Store Manager (HQ)
        User::firstOrCreate(
            ['email' => 'manager.hq@irack.com'],
            [
                'name' => 'Marcus Sterling',
                'password' => Hash::make('Manager@iRACK2026!'),
                'phone' => '+1 (555) 010-0002',
                'role_id' => $storeManagerRole->id,
                'store_id' => $hqStore?->id,
                'is_active' => true,
            ]
        );

        // 3. Sales Manager
        User::firstOrCreate(
            ['email' => 'sales@irack.com'],
            [
                'name' => 'Elena Rostova',
                'password' => Hash::make('Sales@iRACK2026!'),
                'phone' => '+1 (555) 010-0003',
                'role_id' => $salesManagerRole->id,
                'store_id' => $downtownStore?->id,
                'is_active' => true,
            ]
        );

        // 4. Inventory Officer
        User::firstOrCreate(
            ['email' => 'inventory@irack.com'],
            [
                'name' => 'David Chen',
                'password' => Hash::make('Inventory@iRACK2026!'),
                'phone' => '+1 (555) 010-0004',
                'role_id' => $inventoryOfficerRole->id,
                'store_id' => $hqStore?->id,
                'is_active' => true,
            ]
        );
    }
}
