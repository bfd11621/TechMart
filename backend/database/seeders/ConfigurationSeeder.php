<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WebsiteContent;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        // 1. Seed Homepage Banners
        $banners = [
            [
                'title' => 'Next-Generation Compute Infrastructure',
                'subtitle' => 'High-density 2U dual-socket enterprise rack servers engineered for AI workloads, virtualization clusters, and high-performance computing.',
                'badge_text' => 'MISSION-CRITICAL COMPUTING',
                'image_url' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1600&h=650&q=80',
                'cta_button_text' => 'Explore Compute Nodes',
                'cta_button_link' => '/catalog?category=servers',
                'secondary_button_text' => 'Request RFQ Package',
                'secondary_button_link' => '/cart',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Zero-Packet-Loss Spine-Leaf Fabric',
                'subtitle' => 'Ultra-low-latency 400GbE enterprise core switches and aggregation routers with redundant power supplies and hardware telemetry.',
                'badge_text' => 'ENTERPRISE NETWORKING',
                'image_url' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=1600&h=650&q=80',
                'cta_button_text' => 'View Network Switches',
                'cta_button_link' => '/catalog?category=networking',
                'secondary_button_text' => 'Compare Specifications',
                'secondary_button_link' => '/compare',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Continuous Power Protection & Smart UPS',
                'subtitle' => 'High-efficiency Lithium-ion online double-conversion UPS systems and intelligent metered rack PDUs designed for Tier-III & Tier-IV data centers.',
                'badge_text' => 'CLEAN POWER SOLUTIONS',
                'image_url' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=1600&h=650&q=80',
                'cta_button_text' => 'Inspect Power Systems',
                'cta_button_link' => '/catalog?category=power',
                'secondary_button_text' => 'Consult Solutions Engineer',
                'secondary_button_link' => '/contact',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $b) {
            Banner::firstOrCreate(['title' => $b['title']], $b);
        }

        // 2. Seed System Settings
        $settingsData = [
            // Company
            ['group' => 'company', 'key' => 'company_name', 'value' => 'iRACK IT Solution Commercial Co.', 'type' => 'string', 'is_public' => true, 'description' => 'Official corporate entity name'],
            ['group' => 'company', 'key' => 'company_logo_url', 'value' => '/images/logo.svg', 'type' => 'string', 'is_public' => true, 'description' => 'Primary corporate logo URL'],
            ['group' => 'company', 'key' => 'company_description', 'value' => 'Enterprise datacenter servers, spine-leaf networking, SAN/NAS storage, and online power protection systems.', 'type' => 'string', 'is_public' => true, 'description' => 'Brief corporate mission statement'],
            ['group' => 'company', 'key' => 'company_address', 'value' => '3rd Floor, Wastna Commercial Building, General Wingate St, Addis Ababa', 'type' => 'string', 'is_public' => true, 'description' => 'Headquarters physical address'],
            ['group' => 'company', 'key' => 'company_phone', 'value' => '+251 94 642 8560', 'type' => 'string', 'is_public' => true, 'description' => 'Customer solutions hotline'],
            ['group' => 'company', 'key' => 'company_email', 'value' => 'dagimw@iracktech.com', 'type' => 'string', 'is_public' => true, 'description' => 'Official inquiries email address'],
            ['group' => 'company', 'key' => 'company_website', 'value' => 'https://www.irack.com', 'type' => 'string', 'is_public' => true, 'description' => 'Official website domain'],
            ['group' => 'company', 'key' => 'social_links', 'value' => json_encode(['linkedin' => 'https://linkedin.com/company/irack-solutions', 'twitter' => 'https://twitter.com/irack_it', 'youtube' => 'https://youtube.com/@irack-it']), 'type' => 'json', 'is_public' => true, 'description' => 'Social media channels'],

            // System
            ['group' => 'system', 'key' => 'date_format', 'value' => 'Y-m-d', 'type' => 'string', 'is_public' => true, 'description' => 'Standard display date format'],
            ['group' => 'system', 'key' => 'timezone', 'value' => 'Africa/Nairobi', 'type' => 'string', 'is_public' => true, 'description' => 'Application default timezone'],
            ['group' => 'system', 'key' => 'currency_code', 'value' => 'ETB', 'type' => 'string', 'is_public' => true, 'description' => 'Commercial currency code'],
            ['group' => 'system', 'key' => 'currency_symbol', 'value' => 'ETB', 'type' => 'string', 'is_public' => true, 'description' => 'Display currency symbol'],
            ['group' => 'system', 'key' => 'default_quotation_validity_days', 'value' => '30', 'type' => 'integer', 'is_public' => false, 'description' => 'Default price lock period in days'],
            ['group' => 'system', 'key' => 'stock_min_alert_default', 'value' => '5', 'type' => 'integer', 'is_public' => false, 'description' => 'Default minimum stock warning threshold'],
            ['group' => 'system', 'key' => 'inventory_aging_brackets', 'value' => json_encode([30, 60, 90, 180]), 'type' => 'json', 'is_public' => false, 'description' => 'Days brackets for aging reports'],
            ['group' => 'system', 'key' => 'pdf_cr_number', 'value' => '1010892019', 'type' => 'string', 'is_public' => true, 'description' => 'Commercial Registration (CR) number'],
            ['group' => 'system', 'key' => 'pdf_vat_number', 'value' => '310982019200003', 'type' => 'string', 'is_public' => true, 'description' => 'VAT / Tax identification number'],
            ['group' => 'system', 'key' => 'pdf_footer_note', 'value' => 'Certified ISO 9001:2015 Tier-1 hardware distributor. All products backed by OEM manufacturer SLA warranty.', 'type' => 'string', 'is_public' => true, 'description' => 'Legal disclosure on PDF documents'],

            // Merchandising
            ['group' => 'merchandising', 'key' => 'featured_product_ids', 'value' => json_encode([1, 2, 3, 4]), 'type' => 'json', 'is_public' => true, 'description' => 'Pinned product IDs for homepage'],
            ['group' => 'merchandising', 'key' => 'featured_category_ids', 'value' => json_encode([1, 2, 3, 4]), 'type' => 'json', 'is_public' => true, 'description' => 'Pinned category IDs for homepage'],
            ['group' => 'merchandising', 'key' => 'popular_brand_ids', 'value' => json_encode([1, 2, 3, 4]), 'type' => 'json', 'is_public' => true, 'description' => 'Pinned brand IDs for homepage carousel'],
            ['group' => 'merchandising', 'key' => 'show_hero_banner', 'value' => '1', 'type' => 'boolean', 'is_public' => true, 'description' => 'Show homepage hero slider'],
            ['group' => 'merchandising', 'key' => 'show_featured_products', 'value' => '1', 'type' => 'boolean', 'is_public' => true, 'description' => 'Show featured datacenter products'],
            ['group' => 'merchandising', 'key' => 'show_categories_grid', 'value' => '1', 'type' => 'boolean', 'is_public' => true, 'description' => 'Show 9-category hardware grid'],
            ['group' => 'merchandising', 'key' => 'show_brand_carousel', 'value' => '1', 'type' => 'boolean', 'is_public' => true, 'description' => 'Show manufacturer carousel'],

            // Navigation
            ['group' => 'navigation', 'key' => 'header_navigation', 'value' => json_encode([
                ['label' => 'Home', 'path' => '/', 'is_active' => true],
                ['label' => 'Products & Showroom', 'path' => '/catalog', 'is_active' => true],
                ['label' => 'Categories', 'path' => '/categories', 'is_active' => true],
                ['label' => 'Compare', 'path' => '/compare', 'is_active' => true],
                ['label' => 'Brands', 'path' => '/brands', 'is_active' => true],
                ['label' => 'Solutions & Services', 'path' => '/services', 'is_active' => true],
                ['label' => 'About Us', 'path' => '/about', 'is_active' => true],
                ['label' => 'Contact Us', 'path' => '/contact', 'is_active' => true],
            ]), 'type' => 'json', 'is_public' => true, 'description' => 'Header menu items'],

            // Email
            ['group' => 'email', 'key' => 'smtp_host', 'value' => 'smtp.irack.com', 'type' => 'string', 'is_public' => false, 'description' => 'SMTP mail host'],
            ['group' => 'email', 'key' => 'smtp_port', 'value' => '587', 'type' => 'integer', 'is_public' => false, 'description' => 'SMTP mail port'],
            ['group' => 'email', 'key' => 'smtp_username', 'value' => 'notifications@irack.com', 'type' => 'string', 'is_public' => false, 'description' => 'SMTP username'],
            ['group' => 'email', 'key' => 'smtp_encryption', 'value' => 'tls', 'type' => 'string', 'is_public' => false, 'description' => 'SMTP encryption'],
            ['group' => 'email', 'key' => 'mail_from_name', 'value' => 'iRACK IT Solution Solutions Desk', 'type' => 'string', 'is_public' => false, 'description' => 'Sender name'],
            ['group' => 'email', 'key' => 'mail_from_address', 'value' => 'notifications@irack.com', 'type' => 'string', 'is_public' => false, 'description' => 'Sender email address'],
            ['group' => 'email', 'key' => 'rfq_recipients', 'value' => json_encode(['dagimw@iracktech.com', 'admin@irack.com', 'sales@irack.com']), 'type' => 'json', 'is_public' => false, 'description' => 'Staff notification emails for RFQ inquiries'],

            // Notifications
            ['group' => 'notifications', 'key' => 'notify_on_new_rfq', 'value' => '1', 'type' => 'boolean', 'is_public' => false, 'description' => 'Notify on new RFQ'],
            ['group' => 'notifications', 'key' => 'notify_on_low_stock', 'value' => '1', 'type' => 'boolean', 'is_public' => false, 'description' => 'Notify on low stock alert'],
            ['group' => 'notifications', 'key' => 'notify_on_quote_approved', 'value' => '1', 'type' => 'boolean', 'is_public' => false, 'description' => 'Notify on quote approval'],
            ['group' => 'notifications', 'key' => 'notify_on_new_order', 'value' => '1', 'type' => 'boolean', 'is_public' => false, 'description' => 'Notify on confirmed sales order'],
        ];

        foreach ($settingsData as $s) {
            SystemSetting::firstOrCreate(['key' => $s['key']], $s);
        }

        // 3. Seed Website Content CMS Pages
        $pages = [
            [
                'slug' => 'about-us',
                'title' => 'About iRACK IT Solution',
                'subtitle' => 'Pioneering mission-critical enterprise IT infrastructure and high-density datacenter systems across Saudi Arabia and the Middle East.',
                'content' => "Founded with a mission to deliver certified Tier-1 enterprise hardware, iRACK IT Solution stands as the Kingdom's premier distributor of compute nodes, spine-leaf fabrics, high-throughput SAN arrays, and clean uninterruptible power systems.\n\nHeadquartered in Riyadh with distribution facilities in Dammam and Jeddah, our certified team of CCIE, Dell Solutions Architects, and APC Certified Power Engineers partners with government agencies, defense contractors, financial institutions, and telecommunication providers to architect resilient, future-ready infrastructure.",
                'meta_title' => 'About iRACK IT Solution | Enterprise Datacenter Distributor',
                'meta_description' => 'Certified enterprise IT infrastructure distributor in Saudi Arabia. Servers, networking, SAN/NAS storage, and UPS solutions.',
                'sections_data' => [
                    'stats' => [
                        ['label' => 'Certified Hardware Delivered', 'value' => '15,000+'],
                        ['label' => 'Tier-III/IV Facilities Equipped', 'value' => '120+'],
                        ['label' => 'Regional Fulfillment Hubs', 'value' => '3'],
                        ['label' => '24/7 SLA Resolution Time', 'value' => '< 4 hrs'],
                    ],
                ],
                'is_published' => true,
                'updated_by_user_id' => $admin?->id,
            ],
            [
                'slug' => 'services',
                'title' => 'Turnkey Datacenter Services & Solutions',
                'subtitle' => 'Comprehensive engineering capabilities from structured high-density fiber containment to 24/7 mission-critical hardware maintenance SLAs.',
                'content' => "iRACK IT Solution delivers end-to-end datacenter infrastructure engineering services. Whether deploying a greenfield hyperscale facility or modernizing an existing enterprise compute cluster, our vendor-certified engineers ensure flawless execution.\n\nOur service catalog includes thermal containment design, spine-leaf network topology architecture, SAN multi-path configuration, and online UPS load balancing with generator synchronization.",
                'meta_title' => 'Datacenter Engineering Services | iRACK IT Solution',
                'meta_description' => 'Turnkey datacenter containment, structured cabling, storage SAN design, and 24/7 maintenance SLAs.',
                'sections_data' => [
                    'services' => [
                        ['title' => 'Datacenter Thermal Containment', 'description' => 'Hot-aisle and cold-aisle containment systems reducing PUE and optimizing airflow for high-density compute.'],
                        ['title' => 'Spine-Leaf Switching Fabrics', 'description' => 'Non-blocking 100G/400G network architectures with automated BGP EVPN fabric orchestration.'],
                        ['title' => 'All-Flash SAN / NAS Staging', 'description' => 'High-IOPS NVMe-over-Fabrics storage arrays pre-configured with RAID-DP and snapshot protection.'],
                        ['title' => '24/7 Mission-Critical SLA Support', 'description' => 'Dedicated on-site spares inventory with 4-hour certified engineer dispatch guarantees.'],
                    ],
                ],
                'is_published' => true,
                'updated_by_user_id' => $admin?->id,
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Corporate Privacy & Data Governance Policy',
                'subtitle' => 'Our adherence to Saudi National Cybersecurity Authority (NCA) standards and commercial confidentiality.',
                'content' => "iRACK IT Solution ('we', 'our', or 'us') is committed to protecting the organizational and personal information entrusted to us by our corporate clients, partners, and online showroom visitors.\n\n1. Information Collection: We collect corporate credentials, technical Bills of Materials, and procurement inquiries strictly to prepare commercial quotations and fulfill confirmed hardware sales orders.\n\n2. Data Security: All commercial proposals, pricing sheets, and customer contact records are stored in encrypted databases in full compliance with National Data Management Office (NDMO) standards.\n\n3. Non-Disclosure: Client equipment designs, network topologies, and quotation pricing are never shared with unauthorized third parties.",
                'meta_title' => 'Privacy Policy | iRACK IT Solution',
                'meta_description' => 'Corporate data governance and commercial privacy terms governing iRACK IT Solution transactions.',
                'is_published' => true,
                'updated_by_user_id' => $admin?->id,
            ],
            [
                'slug' => 'terms-conditions',
                'title' => 'Standard Terms of Procurement & Commercial Policy',
                'subtitle' => 'Binding terms governing Request for Quotations (RFQs), formal price locks, order confirmation, and OEM warranties.',
                'content' => "1. Request for Quotations (RFQs): Submission of an RFQ through our online showroom does not constitute a legally binding purchase contract or recognized revenue. RFQs serve as engineering inquiries.\n\n2. Quotation Validity: Official commercial quotations issued by iRACK IT Solution carry a binding 30-day price lock unless explicitly stated otherwise in the proposal document.\n\n3. Order Confirmation & Stock Allocation: A sales order is confirmed only upon formal acceptance of the quotation and issuance of a customer purchase order or advance payment. Physical inventory is allocated and deducted upon order confirmation.\n\n4. Warranty & Defect Replacements: All hardware is genuine Tier-1 certified equipment covered by manufacturer warranty SLAs. Defective units will be serviced according to standard OEM return policies.",
                'meta_title' => 'Terms of Procurement | iRACK IT Solution',
                'meta_description' => 'Commercial sales policies, quotation validity, stock allocation terms, and warranty agreements.',
                'is_published' => true,
                'updated_by_user_id' => $admin?->id,
            ],
            [
                'slug' => 'contact-info',
                'title' => 'Contact Solutions Engineering & Regional Hubs',
                'subtitle' => 'Connect directly with our solutions engineering team across our 3 regional distribution hubs.',
                'content' => "Our commercial operations and logistics hubs are strategically located to guarantee same-day equipment dispatch and rapid on-site technical support across the Kingdom of Saudi Arabia.\n\nCentral Distribution Hub (HQ): King Fahd Road, Riyadh &bull; Phone: +966 11 492 8800\nNorth Logistics Depot: King Abdulaziz Port Road, Dammam &bull; Phone: +966 13 881 2290\nWest Coast Commercial Store: Al Andalus District, Jeddah &bull; Phone: +966 12 660 4410",
                'meta_title' => 'Contact Us | iRACK IT Solution',
                'meta_description' => 'Fulfillment hubs in Riyadh, Dammam, and Jeddah. Phone, email, and technical inquiry support.',
                'is_published' => true,
                'updated_by_user_id' => $admin?->id,
            ],
        ];

        foreach ($pages as $p) {
            WebsiteContent::firstOrCreate(['slug' => $p['slug']], $p);
        }
    }
}
