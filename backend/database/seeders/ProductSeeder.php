<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\Store;
use App\Models\StoreInventory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $serverCat = Category::where('name', 'Rackmount Servers (1U & 2U)')->first() ?? Category::first();
        $switchCat = Category::where('name', 'Managed Gigabit & Multi-Gig PoE+ Switches')->first() ?? Category::first();
        $storageCat = Category::where('name', 'Enterprise Rackmount NAS Arrays')->first() ?? Category::first();
        $upsCat = Category::where('name', 'Online Double-Conversion Rack UPS (1kVA - 20kVA)')->first() ?? Category::first();
        $rackCat = Category::where('name', '42U & 48U Server Cabinets (600x1070mm / 800x1200mm)')->first() ?? Category::first();

        $dellBrand = Brand::where('name', 'Dell Technologies')->first() ?? Brand::first();
        $ciscoBrand = Brand::where('name', 'Cisco Systems')->first() ?? Brand::first();
        $synologyBrand = Brand::where('name', 'Synology Inc.')->first() ?? Brand::first();
        $apcBrand = Brand::where('name', 'APC by Schneider Electric')->first() ?? Brand::first();
        $irackBrand = Brand::where('name', 'iRACK Custom Solutions')->first() ?? Brand::first();

        $stores = Store::all();

        $catalog = [
            // 1. Enterprise Server
            [
                'category_id' => $serverCat->id,
                'brand_id' => $dellBrand->id,
                'sku' => 'DEL-PE-R760-01',
                'name' => 'Dell PowerEdge R760 2U Enterprise Server',
                'model_number' => 'PE-R760-2U',
                'short_description' => 'Enterprise 2U dual-socket compute platform designed for intensive virtualization and SQL database performance.',
                'full_description' => 'The Dell PowerEdge R760 delivers exceptional compute density with dual 4th Gen Intel Xeon Scalable processors, DDR5 memory architecture, PCIe Gen5 expansion slots, and iDRAC9 Enterprise remote telemetry.',
                'key_features' => [
                    'Dual Intel Xeon Gold 5418Y Processors (24 Cores / 48 Threads each)',
                    '128GB DDR5 ECC Registered Memory (expandable up to 8TB in 32 DIMMs)',
                    '8x 2.5-inch SAS/SATA/NVMe Hot-Plug Drive Bays with PERC H755 RAID',
                    'Redundant Hot-Plug 800W Titanium Power Supplies (1+1)',
                    'iDRAC9 Enterprise Remote Out-of-Band Management Controller',
                ],
                'base_price' => 5490.00,
                'cost_price' => 4150.00,
                'is_featured' => true,
                'is_published' => true,
                'availability_status' => 'IN_STOCK',
                'warranty_info' => '3 Years ProSupport Next Business Day Onsite',
                'datasheet_url' => '/storage/products/datasheets/datasheet_dell_r760.pdf',
                'datasheet_filename' => 'Dell_PowerEdge_R760_Technical_Datasheet.pdf',
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&auto=format&fit=crop&q=80', 'is_primary' => true],
                ],
                'specs' => [
                    ['group' => 'Processor & Compute', 'name' => 'Processor Type', 'value' => 'Dual 4th Gen Intel Xeon Gold 5418Y'],
                    ['group' => 'Processor & Compute', 'name' => 'Number of CPUs', 'value' => '2 Sockets Populated'],
                    ['group' => 'Processor & Compute', 'name' => 'Total Cores / Threads', 'value' => '48 Cores / 96 Threads (2.0GHz Base, 3.8GHz Turbo)'],
                    ['group' => 'Memory', 'name' => 'Installed RAM', 'value' => '128GB (4x 32GB) DDR5-4800MHz ECC Reg'],
                    ['group' => 'Memory', 'name' => 'Maximum Memory Capacity', 'value' => 'Up to 8TB across 32x DDR5 DIMM slots'],
                    ['group' => 'Storage & RAID', 'name' => 'Drive Bays', 'value' => '8x 2.5" Hot-Plug SAS/SATA/NVMe'],
                    ['group' => 'Storage & RAID', 'name' => 'RAID Controller', 'value' => 'PERC H755 with 8GB NV Flash Cache (RAID 0,1,5,6,10,50,60)'],
                    ['group' => 'Network & I/O', 'name' => 'Network Interfaces', 'value' => 'Broadcom 57416 Dual-Port 10GBASE-T + Dual-Port 1GbE'],
                    ['group' => 'Network & I/O', 'name' => 'PCIe Expansion', 'value' => 'Up to 8x PCIe Gen5 slots'],
                    ['group' => 'Power & Cooling', 'name' => 'Power Supply', 'value' => 'Dual (1+1) 800W Titanium Redundant Hot-Plug'],
                ],
                'stock' => [18, 6, 8],
            ],

            // 2. Enterprise Switch
            [
                'category_id' => $switchCat->id,
                'brand_id' => $ciscoBrand->id,
                'sku' => 'CSC-C9300-48P',
                'name' => 'Cisco Catalyst 9300 48-Port PoE+ Layer 3 Switch',
                'model_number' => 'C9300-48P-A',
                'short_description' => 'Stackable 48-port Gigabit PoE+ campus access switch with modular 10G/25G uplinks and Network Advantage.',
                'full_description' => 'Industry-standard enterprise switch delivering 48 ports of 10/100/1000 PoE+, StackWise-480 stacking fabric, Cisco UPOE support, and automated network segmentation via Cisco DNA Software.',
                'key_features' => [
                    '48x Gigabit Ethernet 10/100/1000 Ports with PoE+ (437W Budget)',
                    '480 Gbps Stacking Bandwidth with StackWise-480 Technology',
                    'Modular Uplink Options supporting 10G SFP+ and 25G SFP28',
                    'Dual Redundant Hot-Swappable AC Power Supplies',
                    'Advanced Layer 3 Dynamic Routing (OSPF, BGP, EIGRP, VRF)',
                ],
                'base_price' => 3850.00,
                'cost_price' => 2800.00,
                'is_featured' => true,
                'is_published' => true,
                'availability_status' => 'IN_STOCK',
                'warranty_info' => 'Cisco Enhanced Limited Lifetime Warranty (E-LLW)',
                'datasheet_url' => '/storage/products/datasheets/datasheet_cisco_c9300.pdf',
                'datasheet_filename' => 'Cisco_Catalyst_9300_Series_Datasheet.pdf',
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=800&auto=format&fit=crop&q=80', 'is_primary' => true],
                ],
                'specs' => [
                    ['group' => 'Port Density', 'name' => 'Number of Ports', 'value' => '48x 10/100/1000 Mbps RJ-45 PoE+'],
                    ['group' => 'Port Density', 'name' => 'Uplink Interfaces', 'value' => 'Modular Network Module (Optional 4x 10G / 2x 25G)'],
                    ['group' => 'Performance', 'name' => 'Switching Capacity', 'value' => '256 Gbps (full duplex)'],
                    ['group' => 'Performance', 'name' => 'Forwarding Rate', 'value' => '190.48 Mpps'],
                    ['group' => 'PoE Capability', 'name' => 'PoE Capacity', 'value' => '437W default (upgradeable to 1440W with secondary PSU)'],
                    ['group' => 'Protocols', 'name' => 'Supported Protocols', 'value' => 'IEEE 802.1Q VLANs, OSPFv3, BGP4, EIGRP, VRF-Lite, PBR'],
                    ['group' => 'Stacking', 'name' => 'Stacking Bandwidth', 'value' => '480 Gbps stacking up to 8 units'],
                ],
                'stock' => [24, 10, 12],
            ],

            // 3. Enterprise Storage NAS
            [
                'category_id' => $storageCat->id,
                'brand_id' => $synologyBrand->id,
                'sku' => 'SYN-RS3621RPXS',
                'name' => 'Synology RackStation RS3621RPxs 12-Bay Enterprise NAS',
                'model_number' => 'RS3621RPxs',
                'short_description' => 'High-performance 12-bay 2U rackmount storage server with redundant power and dual PCIe Gen3 expansion.',
                'full_description' => 'Designed for large-scale enterprise data workloads, virtualization, and backup repositories. Features an Intel Xeon 6-core processor, expandable up to 36 drive bays, and 10GbE network connectivity.',
                'key_features' => [
                    'Intel Xeon D-1531 6-Core 2.2GHz Processor',
                    '16GB DDR4 ECC UDIMM (Expandable up to 64GB)',
                    '12x 3.5"/2.5" Hot-Swappable SATA Bays (Expandable to 36 Bays with RX1217RP)',
                    'Over 471,880 4K Random Read IOPS Throughput',
                    'Dual Redundant 500W Power Supply Units',
                ],
                'base_price' => 4190.00,
                'cost_price' => 3100.00,
                'is_featured' => true,
                'is_published' => true,
                'availability_status' => 'IN_STOCK',
                'warranty_info' => '5 Years Synology Replacement Warranty',
                'datasheet_url' => '/storage/products/datasheets/datasheet_synology_rs3621rpxs.pdf',
                'datasheet_filename' => 'Synology_RS3621RPxs_Datasheet.pdf',
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1600132806370-bf17e65e942f?w=800&auto=format&fit=crop&q=80', 'is_primary' => true],
                ],
                'specs' => [
                    ['group' => 'Storage Capacity', 'name' => 'Raw Capacity', 'value' => '216TB (with 12x 18TB HDDs)'],
                    ['group' => 'Storage Capacity', 'name' => 'Usable Capacity', 'value' => 'Approx. 180TB in RAID 6'],
                    ['group' => 'RAID Support', 'name' => 'RAID', 'value' => 'Synology Hybrid RAID, Basic, JBOD, RAID 0, 1, 5, 6, 10'],
                    ['group' => 'Interfaces', 'name' => 'Interfaces', 'value' => '4x 1GbE RJ-45, 2x PCIe 3.0 x8 slots (for 10G/25G SFP+ cards), 2x USB 3.2'],
                    ['group' => 'Performance', 'name' => 'Performance', 'value' => '471,880 4K Random Read IOPS / 4,315 MB/s Sequential Read'],
                ],
                'stock' => [12, 4, 6],
            ],

            // 4. Power & UPS
            [
                'category_id' => $upsCat->id,
                'brand_id' => $apcBrand->id,
                'sku' => 'APC-SRT-5000RMXLI',
                'name' => 'APC Smart-UPS On-Line 5000VA 230V 3U Rackmount UPS',
                'model_number' => 'SRT5KRMXLI',
                'short_description' => 'True on-line double conversion 5kVA rackmount UPS with embedded network management card NMC3.',
                'full_description' => 'Pure sine-wave power conditioning and zero-transfer-time battery protection for mission-critical datacenter infrastructure, telecom switches, and server clusters.',
                'key_features' => [
                    '5000 VA / 4500 Watts High-Density Clean Sine-Wave Output',
                    'True On-Line Double Conversion Topology with Zero Transfer Time',
                    'Pre-installed AP9641 SmartSlot Network Card with Web/SNMP Telemetry',
                    'Hot-Swappable Leakproof Sealed Lead-Acid Battery Modules',
                    'Emergency Power Off (EPO) Terminal & SmartSlot Expansion',
                ],
                'base_price' => 3790.00,
                'cost_price' => 2750.00,
                'is_featured' => false,
                'is_published' => true,
                'availability_status' => 'IN_STOCK',
                'warranty_info' => '3 Years Electronics / 2 Years Battery Warranty',
                'datasheet_url' => '/storage/products/datasheets/datasheet_apc_srt5k.pdf',
                'datasheet_filename' => 'APC_SmartUPS_RT_5000VA_Datasheet.pdf',
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=800&auto=format&fit=crop&q=80', 'is_primary' => true],
                ],
                'specs' => [
                    ['group' => 'Power Rating', 'name' => 'Capacity', 'value' => '5000 VA / 4500 Watts (0.9 Power Factor)'],
                    ['group' => 'Electrical Input', 'name' => 'Input Voltage', 'value' => '220V / 230V / 240V AC (160V - 275V Operating Range)'],
                    ['group' => 'Electrical Output', 'name' => 'Output Voltage', 'value' => '230V Pure Sine Wave (± 2% Regulation)'],
                    ['group' => 'Battery Configuration', 'name' => 'Battery Configuration', 'value' => 'Maintenance-free sealed Lead-Acid (192V bus)'],
                    ['group' => 'Runtime', 'name' => 'Backup Time', 'value' => '4.2 minutes at full 4500W load; 11.8 minutes at 50% load'],
                ],
                'stock' => [15, 6, 8],
            ],

            // 5. Data Center Cabinet
            [
                'category_id' => $rackCat->id,
                'brand_id' => $irackBrand->id,
                'sku' => 'IRK-RC-42U-1070',
                'name' => 'iRACK Titan Pro 42U Heavy-Duty Server Cabinet (600x1070mm)',
                'model_number' => 'TITAN-42U-HD',
                'short_description' => 'Heavy-duty 19" 42U server cabinet with 80% honeycomb high-flow mesh doors and adjustable mounting depths.',
                'full_description' => 'Constructed from SPCC cold-rolled steel with reinforced corner joints, providing 1,500 kg static load capacity. Includes dual vertical PDU brackets, integrated cable raceways, and grounding lugs.',
                'key_features' => [
                    '42U Rack Height with 19-inch EIA-310-E Standard Compliant Rails',
                    '1,500 kg (3,300 lbs) Static Weight Load Capacity',
                    '80% Perforated Honeycomb Airflow Ventilation Doors',
                    'Pre-Installed Tool-less Vertical PDU & Cable Management Channels',
                    'Keyed Swing-Handle Cam Locks with Multi-Point Security Rods',
                ],
                'base_price' => 1250.00,
                'cost_price' => 780.00,
                'is_featured' => true,
                'is_published' => true,
                'availability_status' => 'IN_STOCK',
                'warranty_info' => '5 Years Structural Chassis Warranty',
                'datasheet_url' => '/storage/products/datasheets/datasheet_irack_titan_42u.pdf',
                'datasheet_filename' => 'iRACK_Titan_42U_Technical_Datasheet.pdf',
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&auto=format&fit=crop&q=80', 'is_primary' => true],
                ],
                'specs' => [
                    ['group' => 'Physical Dimensions', 'name' => 'Rack Units', 'value' => '42U (19-inch standard)'],
                    ['group' => 'Physical Dimensions', 'name' => 'Dimensions (WxDxH)', 'value' => '600 mm x 1070 mm x 2050 mm'],
                    ['group' => 'Load Capacity', 'name' => 'Static Load Capacity', 'value' => '1,500 kg (3,300 lbs)'],
                    ['group' => 'Load Capacity', 'name' => 'Dynamic Load Capacity', 'value' => '1,000 kg (2,200 lbs)'],
                    ['group' => 'Ventilation & Thermal', 'name' => 'Perforation Rate', 'value' => '80% High-Flow Honeycomb Hex Mesh'],
                ],
                'stock' => [35, 12, 18],
            ],
        ];

        foreach ($catalog as $pData) {
            $product = Product::firstOrCreate(['sku' => $pData['sku']], [
                'category_id' => $pData['category_id'],
                'brand_id' => $pData['brand_id'],
                'sku' => $pData['sku'],
                'name' => $pData['name'],
                'slug' => Str::slug($pData['name']),
                'model_number' => $pData['model_number'],
                'short_description' => $pData['short_description'],
                'full_description' => $pData['full_description'],
                'key_features' => $pData['key_features'],
                'base_price' => $pData['base_price'],
                'cost_price' => $pData['cost_price'],
                'is_featured' => $pData['is_featured'],
                'is_published' => $pData['is_published'],
                'availability_status' => $pData['availability_status'],
                'warranty_info' => $pData['warranty_info'],
                'datasheet_url' => $pData['datasheet_url'],
                'datasheet_filename' => $pData['datasheet_filename'],
            ]);

            // Sync Specifications
            $sort = 1;
            foreach ($pData['specs'] as $spec) {
                ProductSpecification::firstOrCreate([
                    'product_id' => $product->id,
                    'spec_name' => $spec['name'],
                ], [
                    'spec_group' => $spec['group'],
                    'spec_value' => $spec['value'],
                    'sort_order' => $sort++,
                ]);
            }

            // Sync Images
            $imgSort = 1;
            foreach ($pData['images'] as $img) {
                ProductImage::firstOrCreate([
                    'product_id' => $product->id,
                    'image_url' => $img['url'],
                ], [
                    'is_primary' => $img['is_primary'],
                    'sort_order' => $imgSort++,
                ]);
            }

            // Sync Store Inventory
            foreach ($stores as $idx => $store) {
                $qty = $pData['stock'][$idx] ?? 10;
                StoreInventory::firstOrCreate([
                    'store_id' => $store->id,
                    'product_id' => $product->id,
                ], [
                    'quantity' => $qty,
                    'reserved_quantity' => 0,
                    'min_alert_quantity' => 5,
                    'shelf_location' => 'AISLE-' . chr(65 + $idx) . '-BAY-' . ($idx + 1),
                ]);
            }
        }
    }
}
