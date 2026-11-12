<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryAndBrandSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Partner Brands
        $brands = [
            ['name' => 'Cisco Systems', 'website' => 'https://cisco.com', 'description' => 'Enterprise routing, switching, and datacenter networking.'],
            ['name' => 'Dell Technologies', 'website' => 'https://dell.com', 'description' => 'PowerEdge rack servers, Precision workstations, and PowerVault storage.'],
            ['name' => 'Hewlett Packard Enterprise', 'website' => 'https://hpe.com', 'description' => 'ProLiant servers, Aruba networking, and Nimble storage.'],
            ['name' => 'APC by Schneider Electric', 'website' => 'https://apc.com', 'description' => 'Datacenter UPS systems, smart rack PDUs, and NetShelter enclosures.'],
            ['name' => 'Fortinet', 'website' => 'https://fortinet.com', 'description' => 'FortiGate high-throughput enterprise firewalls and cybersecurity.'],
            ['name' => 'Ubiquiti Inc.', 'website' => 'https://ui.com', 'description' => 'UniFi high-density routing, switching, and building bridges.'],
            ['name' => 'Synology Inc.', 'website' => 'https://synology.com', 'description' => 'Rackmount NAS/SAN enterprise unified data storage solutions.'],
            ['name' => 'Eaton', 'website' => 'https://eaton.com', 'description' => 'Mission-critical backup power, backup generators, and electrical distribution.'],
            ['name' => 'iRACK Custom Solutions', 'website' => 'https://irack.com', 'description' => 'High-load 42U/48U server racks, smart containment, and structured patch systems.'],
        ];

        foreach ($brands as $b) {
            Brand::firstOrCreate(['slug' => Str::slug($b['name'])], [
                'name' => $b['name'],
                'slug' => Str::slug($b['name']),
                'website' => $b['website'],
                'description' => $b['description'],
                'is_active' => true,
            ]);
        }

        // 2. The 9 Required Initial Categories with Subcategories
        $categoriesStructure = [
            1 => [
                'name' => 'Servers and Workstations',
                'description' => '1U/2U/4U rackmount compute nodes, blade enclosures, and tower engineering workstations.',
                'children' => [
                    'Rackmount Servers (1U & 2U)',
                    'Multi-Node High-Density Servers',
                    'GPU & AI Acceleration Nodes',
                    'Tower Engineering Workstations',
                ],
            ],
            2 => [
                'name' => 'Networking Equipment',
                'description' => 'Layer 2/3 enterprise managed switches, aggregation switches, core routers, and access points.',
                'children' => [
                    'Managed Gigabit & Multi-Gig PoE+ Switches',
                    '10G / 25G / 100G SFP+ Datacenter Switches',
                    'Core Enterprise Edge Routers',
                    'Enterprise Wi-Fi 6E/7 Access Points',
                ],
            ],
            3 => [
                'name' => 'Storage Solutions',
                'description' => 'Rackmount NAS storage, SAN fiber channel arrays, and direct attached storage expansions.',
                'children' => [
                    'Enterprise Rackmount NAS Arrays',
                    'All-Flash NVMe & SAS SAN Storage',
                    'JBOD Storage Expansion Enclosures',
                ],
            ],
            4 => [
                'name' => 'Power Solutions',
                'description' => 'On-line double-conversion UPS systems, intelligent switched PDUs, and power backup modules.',
                'children' => [
                    'Online Double-Conversion Rack UPS (1kVA - 20kVA)',
                    'Intelligent Metered & Switched PDUs',
                    'Automatic Transfer Switches (ATS)',
                    'Extended Runtime Battery Packs',
                ],
            ],
            5 => [
                'name' => 'Security Solutions',
                'description' => 'Next-generation firewalls (NGFW), intrusion prevention systems, and physical datacenter access control.',
                'children' => [
                    'Next-Gen Enterprise Firewalls (NGFW)',
                    'Datacenter Biometric Rack Locks',
                    'Environmental & Surveillance Monitoring',
                ],
            ],
            6 => [
                'name' => 'Audio Visual Solutions',
                'description' => 'Command center video wall controllers, conference room matrix switchers, and digital AV over IP.',
                'children' => [
                    'AV over IP Encoders & Decoders',
                    '4K Matrix Presentation Switchers',
                    'Rackmount Video Audio Processors',
                ],
            ],
            7 => [
                'name' => 'Computers and Accessories',
                'description' => 'Commercial client desktops, rack-mounted KVM drawers, and high-performance client accessories.',
                'children' => [
                    'Commercial Micro/SFF Desktops',
                    '1U Rackmount LCD Console KVM Drawers',
                    'KVM over IP Multi-User Switches',
                ],
            ],
            8 => [
                'name' => 'Data Center Infrastructure',
                'description' => 'Standard 19-inch 42U/48U server enclosures, open frame 4-post racks, and aisle containment.',
                'children' => [
                    '42U & 48U Server Cabinets (600x1070mm / 800x1200mm)',
                    'Wall Mount Network Cabinets (9U - 24U)',
                    'Open Frame 4-Post Equipment Racks',
                    'Aisle Containment & Smart Cooling Doors',
                ],
            ],
            9 => [
                'name' => 'IT Accessories',
                'description' => 'Structured fiber/copper cabling, horizontal cable organizers, PDU power cords, and cage nuts.',
                'children' => [
                    'Cat6A / Cat7 Shielded Patch Panels',
                    'MPO & LC Fiber Optic Patch Cassettes',
                    'Horizontal & Vertical Cable Managers',
                    'Heavy-Duty Rack Shelves & Rail Kits',
                ],
            ],
        ];

        foreach ($categoriesStructure as $sort => $cat) {
            $parent = Category::firstOrCreate(['slug' => Str::slug($cat['name'])], [
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
                'sort_order' => $sort,
                'is_active' => true,
            ]);

            $childSort = 1;
            foreach ($cat['children'] as $childName) {
                Category::firstOrCreate(['slug' => Str::slug($childName)], [
                    'parent_id' => $parent->id,
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'sort_order' => $childSort++,
                    'is_active' => true,
                ]);
            }
        }
    }
}
