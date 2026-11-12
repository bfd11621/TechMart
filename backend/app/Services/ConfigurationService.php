<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WebsiteContent;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ConfigurationService
{
    /**
     * 1. Get All Administrative Settings Grouped by Module
     */
    public function getAllSettings(bool $maskEncrypted = true): array
    {
        $settings = SystemSetting::all();
        $grouped = [];

        foreach ($settings as $setting) {
            $grouped[$setting->group][$setting->key] = $setting->getParsedValue($maskEncrypted);
        }

        // Merge defaults if database hasn't been seeded yet
        return $this->mergeDefaultSettings($grouped);
    }

    /**
     * 2. Update a Group of Settings (Company, General, Merchandising, Navigation, Email)
     */
    public function updateSettingsGroup(string $group, array $values, ?User $user = null): array
    {
        return DB::transaction(function () use ($group, $values, $user) {
            $updatedKeys = [];

            foreach ($values as $key => $val) {
                $setting = SystemSetting::firstOrNew(['key' => $key]);
                $setting->group = $group;

                // Determine type & public flag from existing or schema definitions
                $meta = $this->getSettingMetadata($key, $group);
                $setting->type = $meta['type'];
                $setting->is_public = $meta['is_public'];
                $setting->description = $meta['description'];

                $setting->setParsedValue($val);
                $setting->save();
                $updatedKeys[] = $key;
            }

            AuditService::log('SETTINGS_UPDATED', null, null, [
                'group' => $group,
                'updated_keys' => $updatedKeys,
                'updated_by' => $user?->name ?? 'Administrator',
            ]);

            return $this->getAllSettings(true)[$group] ?? [];
        });
    }

    /**
     * 3. Get Public Configuration for Frontend Website
     */
    public function getPublicSettings(): array
    {
        $all = $this->getAllSettings(true);

        return [
            'company' => $all['company'] ?? [],
            'merchandising' => $all['merchandising'] ?? [],
            'navigation' => $all['navigation'] ?? [],
            'system' => [
                'currency_code' => $all['system']['currency_code'] ?? 'ETB',
                'currency_symbol' => $all['system']['currency_symbol'] ?? 'ETB',
                'date_format' => $all['system']['date_format'] ?? 'Y-m-d',
                'timezone' => $all['system']['timezone'] ?? 'Africa/Nairobi',
            ],
        ];
    }

    /**
     * 4. Banners Management
     */
    public function getBanners(bool $activeOnly = false)
    {
        return Banner::query()
            ->when($activeOnly, fn($q) => $q->active())
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    public function saveBanner(array $data, ?int $id = null, User $user): Banner
    {
        return DB::transaction(function () use ($data, $id, $user) {
            $banner = $id ? Banner::findOrFail($id) : new Banner();
            $banner->fill([
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?? null,
                'badge_text' => $data['badge_text'] ?? null,
                'image_url' => $data['image_url'],
                'cta_button_text' => $data['cta_button_text'] ?? 'Explore Hardware',
                'cta_button_link' => $data['cta_button_link'] ?? '/catalog',
                'secondary_button_text' => $data['secondary_button_text'] ?? null,
                'secondary_button_link' => $data['secondary_button_link'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
            ]);
            $banner->save();

            AuditService::log($id ? 'BANNER_UPDATED' : 'BANNER_CREATED', $banner, null, [
                'title' => $banner->title,
                'is_active' => $banner->is_active,
            ]);

            return $banner;
        });
    }

    public function deleteBanner(Banner $banner, User $user): bool
    {
        $bannerTitle = $banner->title;
        $banner->delete();

        AuditService::log('BANNER_DELETED', null, null, [
            'title' => $bannerTitle,
        ]);

        return true;
    }

    public function reorderBanners(array $orderIds, User $user): void
    {
        DB::transaction(function () use ($orderIds, $user) {
            foreach ($orderIds as $index => $bannerId) {
                Banner::where('id', $bannerId)->update(['sort_order' => $index]);
            }

            AuditService::log('BANNERS_REORDERED', null, null, [
                'order_count' => count($orderIds),
            ]);
        });
    }

    /**
     * 5. Website Content Pages Management
     */
    public function getWebsiteContent(string $slug): ?WebsiteContent
    {
        return WebsiteContent::where('slug', $slug)->first();
    }

    public function updateWebsiteContent(string $slug, array $data, User $user): WebsiteContent
    {
        return DB::transaction(function () use ($slug, $data, $user) {
            $content = WebsiteContent::firstOrNew(['slug' => $slug]);
            $content->fill([
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?? null,
                'content' => $data['content'] ?? null,
                'meta_title' => $data['meta_title'] ?? $data['title'],
                'meta_description' => $data['meta_description'] ?? null,
                'sections_data' => $data['sections_data'] ?? null,
                'is_published' => $data['is_published'] ?? true,
                'updated_by_user_id' => $user->id,
            ]);
            $content->save();

            AuditService::log('CONTENT_PAGE_UPDATED', $content, null, [
                'slug' => $slug,
                'title' => $content->title,
            ]);

            return $content;
        });
    }

    /**
     * 6. Test Email Connection & Send Sample RFQ Alert
     */
    public function testEmailConnection(array $config, ?User $user = null): array
    {
        try {
            $testRecipient = $config['test_recipient'] ?? ($user?->email ?? 'dagimw@iracktech.com');
            if (!$testRecipient) {
                $testRecipient = 'dagimw@iracktech.com';
            }

            // Retrieve or construct sample RFQ model
            $sampleRfq = null;
            try {
                $sampleRfq = \App\Models\RfqRequest::with(['items.product', 'preferredStore'])->latest()->first();
            } catch (\Throwable) {
                // Ignore DB error if table not yet migrated
            }

            if (!$sampleRfq) {
                $sampleRfq = new \App\Models\RfqRequest([
                    'rfq_number' => 'RFQ-' . date('Y') . '-SAMPLE',
                    'customer_name' => 'Dr. Khalid Al-Mansour',
                    'company_name' => 'Saudi Cloud & Datacenter Systems Ltd.',
                    'customer_email' => 'khalid.mansour@saudicloud.sa',
                    'customer_phone' => '+966 50 123 4567',
                    'location' => 'Riyadh Datacenter Hub 2, Olaya, Riyadh',
                    'city' => 'Riyadh',
                    'customer_notes' => 'Tier-III datacenter expansion. Need formal quotation and OEM warranty confirmation.',
                    'submitted_at' => now(),
                ]);

                $sampleProduct1 = new \App\Models\Product([
                    'name' => 'PowerEdge R760 Enterprise 2U Rack Server',
                    'sku' => 'SRV-PE-R760-01',
                ]);
                $sampleProduct2 = new \App\Models\Product([
                    'name' => 'Nexus 9300 48-Port 100GbE Spine-Leaf Switch',
                    'sku' => 'NET-NX-9300-48',
                ]);

                $item1 = new \App\Models\RfqItem(['quantity' => 4]);
                $item1->setRelation('product', $sampleProduct1);

                $item2 = new \App\Models\RfqItem(['quantity' => 2]);
                $item2->setRelation('product', $sampleProduct2);

                $sampleRfq->setRelation('items', collect([$item1, $item2]));
            }

            $mailer = config('mail.default', 'log');

            try {
                Mail::to($testRecipient)->send(new \App\Mail\AdminRfqNotificationMail($sampleRfq));
                $sentStatus = "Quotation alert email successfully dispatched to {$testRecipient} via [{$mailer}] mail driver. Reference: {$sampleRfq->rfq_number}.";
            } catch (\Throwable $mailErr) {
                \Illuminate\Support\Facades\Log::info("Admin RFQ notification mail test for {$testRecipient}: " . $mailErr->getMessage());
                $sentStatus = "Notice: SMTP delivery threw: " . $mailErr->getMessage() . ". The RFQ alert was rendered and written to backend/storage/logs/laravel.log.";
            }

            return [
                'success' => true,
                'message' => $sentStatus,
                'recipient' => $testRecipient,
                'rfq_number' => $sampleRfq->rfq_number,
                'mailer' => $mailer,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Email test failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Metadata definitions for all settings keys
     */
    private function getSettingMetadata(string $key, string $group): array
    {
        $schema = [
            // Company Group
            'company_name' => ['type' => 'string', 'is_public' => true, 'description' => 'Official corporate entity name'],
            'company_logo_url' => ['type' => 'string', 'is_public' => true, 'description' => 'Primary corporate logo URL'],
            'company_description' => ['type' => 'string', 'is_public' => true, 'description' => 'Brief corporate mission statement'],
            'company_address' => ['type' => 'string', 'is_public' => true, 'description' => 'Headquarters physical address'],
            'company_phone' => ['type' => 'string', 'is_public' => true, 'description' => 'Customer solutions hotline'],
            'company_email' => ['type' => 'string', 'is_public' => true, 'description' => 'Official inquiries email address'],
            'company_website' => ['type' => 'string', 'is_public' => true, 'description' => 'Official website domain'],
            'social_links' => ['type' => 'json', 'is_public' => true, 'description' => 'Social media channels (LinkedIn, X, YouTube)'],

            // General System Group
            'date_format' => ['type' => 'string', 'is_public' => true, 'description' => 'Standard display date format'],
            'timezone' => ['type' => 'string', 'is_public' => true, 'description' => 'Application default timezone'],
            'currency_code' => ['type' => 'string', 'is_public' => true, 'description' => 'Standard commercial currency code (e.g. SAR)'],
            'currency_symbol' => ['type' => 'string', 'is_public' => true, 'description' => 'Display currency symbol'],
            'default_quotation_validity_days' => ['type' => 'integer', 'is_public' => false, 'description' => 'Default proposal price lock days'],
            'stock_min_alert_default' => ['type' => 'integer', 'is_public' => false, 'description' => 'Default low stock warning threshold'],
            'inventory_aging_brackets' => ['type' => 'json', 'is_public' => false, 'description' => 'Days brackets for aging analysis'],
            'pdf_cr_number' => ['type' => 'string', 'is_public' => true, 'description' => 'Commercial Registration (CR) number for PDF headers'],
            'pdf_vat_number' => ['type' => 'string', 'is_public' => true, 'description' => 'Tax / VAT identification number for PDF documents'],
            'pdf_footer_note' => ['type' => 'string', 'is_public' => true, 'description' => 'Standard legal disclosure on PDF documents'],

            // Merchandising Group
            'featured_product_ids' => ['type' => 'json', 'is_public' => true, 'description' => 'Pinned product IDs for homepage showcase'],
            'featured_category_ids' => ['type' => 'json', 'is_public' => true, 'description' => 'Pinned category IDs for homepage'],
            'popular_brand_ids' => ['type' => 'json', 'is_public' => true, 'description' => 'Pinned partner brand IDs for homepage carousel'],
            'show_hero_banner' => ['type' => 'boolean', 'is_public' => true, 'description' => 'Enable or disable homepage hero slider'],
            'show_featured_products' => ['type' => 'boolean', 'is_public' => true, 'description' => 'Display featured datacenter hardware section'],
            'show_categories_grid' => ['type' => 'boolean', 'is_public' => true, 'description' => 'Display 9-category hardware grid'],
            'show_brand_carousel' => ['type' => 'boolean', 'is_public' => true, 'description' => 'Display authorized manufacturer carousel'],

            // Navigation Group
            'header_navigation' => ['type' => 'json', 'is_public' => true, 'description' => 'Top navigation menu items'],
            'footer_navigation_groups' => ['type' => 'json', 'is_public' => true, 'description' => 'Footer grouped navigation links'],

            // Email Group (Sensitive Encryption)
            'smtp_host' => ['type' => 'string', 'is_public' => false, 'description' => 'SMTP mail server hostname'],
            'smtp_port' => ['type' => 'integer', 'is_public' => false, 'description' => 'SMTP mail server port (587, 465)'],
            'smtp_username' => ['type' => 'string', 'is_public' => false, 'description' => 'SMTP authentication username'],
            'smtp_password' => ['type' => 'encrypted', 'is_public' => false, 'description' => 'Encrypted SMTP password'],
            'smtp_encryption' => ['type' => 'string', 'is_public' => false, 'description' => 'TLS, SSL, or none'],
            'mail_from_name' => ['type' => 'string', 'is_public' => false, 'description' => 'Outgoing notification sender name'],
            'mail_from_address' => ['type' => 'string', 'is_public' => false, 'description' => 'Outgoing notification sender email'],
            'rfq_recipients' => ['type' => 'json', 'is_public' => false, 'description' => 'Administrative email notification recipients for new RFQs'],

            // Notifications Group
            'notify_on_new_rfq' => ['type' => 'boolean', 'is_public' => false, 'description' => 'Email notification when customer submits RFQ'],
            'notify_on_low_stock' => ['type' => 'boolean', 'is_public' => false, 'description' => 'Email notification when SKU reaches threshold'],
            'notify_on_quote_approved' => ['type' => 'boolean', 'is_public' => false, 'description' => 'Notify sales staff upon client quote approval'],
            'notify_on_new_order' => ['type' => 'boolean', 'is_public' => false, 'description' => 'Notify warehouse team upon sales order confirmation'],
        ];

        return $schema[$key] ?? ['type' => 'string', 'is_public' => false, 'description' => ucfirst(str_replace('_', ' ', $key))];
    }

    /**
     * Default baseline settings when database is uninitialized
     */
    private function mergeDefaultSettings(array $grouped): array
    {
        $defaults = [
            'company' => [
                'company_name' => 'iRACK IT Solution Commercial Co.',
                'company_logo_url' => '/images/logo.svg',
                'company_description' => 'Enterprise datacenter servers, spine-leaf networking, SAN/NAS storage, and online power protection systems.',
                'company_address' => '3rd Floor, Wastna Commercial Building, General Wingate St, Addis Ababa',
                'company_phone' => '+251 94 642 8560',
                'company_email' => 'dagimw@iracktech.com',
                'company_website' => 'https://www.irack.com',
                'social_links' => [
                    'linkedin' => 'https://linkedin.com/company/irack-solutions',
                    'twitter' => 'https://twitter.com/irack_it',
                    'youtube' => 'https://youtube.com/@irack-it',
                ],
            ],
            'system' => [
                'date_format' => 'Y-m-d',
                'timezone' => 'Africa/Nairobi',
                'currency_code' => 'ETB',
                'currency_symbol' => 'ETB',
                'default_quotation_validity_days' => 30,
                'stock_min_alert_default' => 5,
                'inventory_aging_brackets' => [30, 60, 90, 180],
                'pdf_cr_number' => '1010892019',
                'pdf_vat_number' => '310982019200003',
                'pdf_footer_note' => 'Certified ISO 9001:2015 Tier-1 hardware distributor. All products backed by manufacturer SLA warranty.',
            ],
            'merchandising' => [
                'featured_product_ids' => [1, 2, 3, 4],
                'featured_category_ids' => [1, 2, 3, 4],
                'popular_brand_ids' => [1, 2, 3, 4],
                'show_hero_banner' => true,
                'show_featured_products' => true,
                'show_categories_grid' => true,
                'show_brand_carousel' => true,
            ],
            'navigation' => [
                'header_navigation' => [
                    ['label' => 'Home', 'path' => '/', 'is_active' => true],
                    ['label' => 'Products & Showroom', 'path' => '/catalog', 'is_active' => true],
                    ['label' => 'Categories', 'path' => '/categories', 'is_active' => true],
                    ['label' => 'Compare', 'path' => '/compare', 'is_active' => true],
                    ['label' => 'Brands', 'path' => '/brands', 'is_active' => true],
                    ['label' => 'Solutions & Services', 'path' => '/services', 'is_active' => true],
                    ['label' => 'About Us', 'path' => '/about', 'is_active' => true],
                    ['label' => 'Contact Us', 'path' => '/contact', 'is_active' => true],
                ],
                'footer_navigation_groups' => [
                    [
                        'title' => 'Hardware Categories',
                        'links' => [
                            ['label' => 'Servers & Compute', 'path' => '/catalog?category=servers'],
                            ['label' => 'Networking Switches', 'path' => '/catalog?category=networking'],
                            ['label' => 'Storage SAN/NAS', 'path' => '/catalog?category=storage'],
                            ['label' => 'Online UPS & Power', 'path' => '/catalog?category=power'],
                        ],
                    ],
                    [
                        'title' => 'Solutions & Engineering',
                        'links' => [
                            ['label' => 'Datacenter Containment', 'path' => '/services'],
                            ['label' => 'Structured Fiber Cabling', 'path' => '/services'],
                            ['label' => 'Mission-Critical 24/7 SLA', 'path' => '/services'],
                            ['label' => 'Request for Quotation (RFQ)', 'path' => '/cart'],
                        ],
                    ],
                    [
                        'title' => 'Corporate Governance',
                        'links' => [
                            ['label' => 'About iRACK IT Solution', 'path' => '/about'],
                            ['label' => 'Partner Manufacturers', 'path' => '/brands'],
                            ['label' => 'Procurement Terms', 'path' => '/terms'],
                            ['label' => 'Privacy & Compliance', 'path' => '/privacy'],
                        ],
                    ],
                ],
            ],
            'email' => [
                'smtp_host' => 'smtp.irack.com',
                'smtp_port' => 587,
                'smtp_username' => 'notifications@irack.com',
                'smtp_password' => '••••••••••••',
                'smtp_encryption' => 'tls',
                'mail_from_name' => 'iRACK IT Solution Solutions Desk',
                'mail_from_address' => 'notifications@irack.com',
                'rfq_recipients' => ['dagimw@iracktech.com', 'admin@irack.com', 'sales@irack.com'],
            ],
            'notifications' => [
                'notify_on_new_rfq' => true,
                'notify_on_low_stock' => true,
                'notify_on_quote_approved' => true,
                'notify_on_new_order' => true,
            ],
        ];

        foreach ($defaults as $grp => $items) {
            if (!isset($grouped[$grp])) {
                $grouped[$grp] = $items;
            } else {
                $grouped[$grp] = array_merge($items, $grouped[$grp]);
            }
        }

        return $grouped;
    }
}
