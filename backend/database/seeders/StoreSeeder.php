<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            [
                'code' => 'ST-HQ-01',
                'name' => 'iRACK Central Distribution Hub (HQ)',
                'email' => 'hq-warehouse@irack.com',
                'phone' => '+1 (555) 019-2831',
                'address' => '100 Technology Parkway, Suite 400',
                'city' => 'Metropolis',
                'state' => 'CA',
                'postal_code' => '94016',
                'is_active' => true,
                'is_primary' => true,
            ],
            [
                'code' => 'ST-DOWNTOWN-02',
                'name' => 'iRACK Commercial Sales Center',
                'email' => 'downtown-store@irack.com',
                'phone' => '+1 (555) 019-4822',
                'address' => '450 Innovation Blvd',
                'city' => 'Metropolis',
                'state' => 'CA',
                'postal_code' => '94025',
                'is_active' => true,
                'is_primary' => false,
            ],
            [
                'code' => 'ST-NORTH-03',
                'name' => 'iRACK North Regional Depot',
                'email' => 'north-depot@irack.com',
                'phone' => '+1 (555) 019-9914',
                'address' => '782 Enterprise Way, Bay 12',
                'city' => 'Silicon City',
                'state' => 'CA',
                'postal_code' => '95054',
                'is_active' => true,
                'is_primary' => false,
            ],
        ];

        foreach ($stores as $storeData) {
            Store::firstOrCreate(['code' => $storeData['code']], $storeData);
        }
    }
}
