<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\QuotationStatus;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuotationAndSalesSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $storeHq = Store::where('is_primary', true)->first() ?? Store::first();
        $storeNorth = Store::where('code', 'ST-NOR-02')->first() ?? $storeHq;

        $servers = Product::where('sku', 'like', '%SRV%')->first();
        $switches = Product::where('sku', 'like', '%NET%')->first();
        $storage = Product::where('sku', 'like', '%STR%')->first();
        $ups = Product::where('sku', 'like', '%PWR%')->first();

        if (!$servers || !$admin) {
            return;
        }

        // 1. Approved Quotation ready for conversion
        $q1 = Quotation::firstOrCreate(['quotation_number' => 'QT-2026-X9B21'], [
            'store_id' => $storeHq->id,
            'customer_name' => 'David Sterling',
            'customer_email' => 'd.sterling@apexcloud.io',
            'customer_phone' => '+1 (555) 492-8810',
            'company_name' => 'Apex Cloud Solutions Inc.',
            'billing_address' => '100 Silicon Parkway, Building C, San Jose, CA',
            'status' => QuotationStatus::APPROVED,
            'valid_until' => now()->addDays(30)->toDateString(),
            'subtotal' => 31200.00,
            'discount_type' => 'fixed',
            'discount_value' => 1200.00,
            'discount_amount' => 1200.00,
            'tax_rate' => 15.00,
            'tax_amount' => 4500.00,
            'total_amount' => 34500.00,
            'terms_conditions' => 'Standard 30-day corporate price lock. Delivery within 7-10 business days.',
            'notes' => 'Enterprise discount approved by Sales Director.',
            'prepared_by_user_id' => $admin->id,
            'approved_at' => now()->subDay(),
        ]);

        if ($q1->items()->count() === 0) {
            $q1->items()->createMany([
                [
                    'product_id' => $servers->id,
                    'quantity' => 4,
                    'unit_price' => 5200.00,
                    'discount_percent' => 0.00,
                    'total_price' => 20800.00,
                    'notes' => 'Dual Xeon Silver, 128GB DDR5 ECC.',
                ],
                [
                    'product_id' => $switches ? $switches->id : $servers->id,
                    'quantity' => 2,
                    'unit_price' => 5200.00,
                    'discount_percent' => 0.00,
                    'total_price' => 10400.00,
                    'notes' => 'Redundant 1100W AC power supplies.',
                ],
            ]);

            $q1->statusHistories()->create([
                'status' => QuotationStatus::APPROVED->value,
                'previous_status' => QuotationStatus::SENT->value,
                'user_id' => $admin->id,
                'notes' => 'Quotation signed and approved by client CTO.',
            ]);
        }

        // 2. Converted Quotation into Confirmed Sales Order
        $q2 = Quotation::firstOrCreate(['quotation_number' => 'QT-2026-P3R19'], [
            'store_id' => $storeHq->id,
            'customer_name' => 'Tariq Al-Mansoor',
            'customer_email' => 'tariq@gulfdata.sa',
            'customer_phone' => '+966 50 123 4567',
            'company_name' => 'Gulf Cloud Data Centers Ltd',
            'billing_address' => 'King Fahd Road, Al Olaya District, Riyadh 12211, KSA',
            'status' => QuotationStatus::CONVERTED,
            'valid_until' => now()->addDays(20)->toDateString(),
            'subtotal' => 48000.00,
            'discount_type' => 'fixed',
            'discount_value' => 3000.00,
            'discount_amount' => 3000.00,
            'tax_rate' => 15.00,
            'tax_amount' => 6750.00,
            'total_amount' => 51750.00,
            'terms_conditions' => 'Turnkey installation included. 100% advance wire transfer confirmed.',
            'notes' => 'Direct corporate solution drafted for datacenter phase 2 expansion.',
            'prepared_by_user_id' => $admin->id,
            'approved_at' => now()->subDays(3),
            'converted_at' => now()->subDays(2),
        ]);

        if ($q2->items()->count() === 0) {
            $q2->items()->createMany([
                [
                    'product_id' => $ups ? $ups->id : $servers->id,
                    'quantity' => 4,
                    'unit_price' => 6000.00,
                    'discount_percent' => 0.00,
                    'total_price' => 24000.00,
                    'notes' => 'Rackmount 1U/2U with integrated network card.',
                ],
                [
                    'product_id' => $servers->id,
                    'quantity' => 4,
                    'unit_price' => 6000.00,
                    'discount_percent' => 0.00,
                    'total_price' => 24000.00,
                    'notes' => 'Redundant BOSS-N1 boot controllers.',
                ],
            ]);

            $q2->statusHistories()->create([
                'status' => QuotationStatus::CONVERTED->value,
                'previous_status' => QuotationStatus::APPROVED->value,
                'user_id' => $admin->id,
                'notes' => 'Converted into active sales order with inventory deduction.',
            ]);
        }

        // Create the corresponding confirmed sales order for Q2
        $so1 = SalesOrder::firstOrCreate(['order_number' => 'SO-2026-G8K22'], [
            'quotation_id' => $q2->id,
            'store_id' => $storeHq->id,
            'customer_name' => $q2->customer_name,
            'customer_email' => $q2->customer_email,
            'customer_phone' => $q2->customer_phone,
            'company_name' => $q2->company_name,
            'billing_address' => $q2->billing_address,
            'shipping_address' => 'Tier-III Facility, Site 4, Industrial Area 2, Riyadh, KSA',
            'status' => OrderStatus::CONFIRMED,
            'payment_status' => PaymentStatus::PAID,
            'payment_method' => 'BANK_TRANSFER',
            'subtotal' => 48000.00,
            'discount_amount' => 3000.00,
            'tax_amount' => 6750.00,
            'total_amount' => 51750.00,
            'paid_amount' => 51750.00,
            'created_by_user_id' => $admin->id,
            'confirmed_by_user_id' => $admin->id,
            'confirmed_at' => now()->subDays(2),
            'notes' => 'Converted from proposal QT-2026-P3R19. Delivery scheduled.',
        ]);

        if ($so1->items()->count() === 0) {
            $so1->items()->createMany([
                [
                    'product_id' => $ups ? $ups->id : $servers->id,
                    'quantity' => 4,
                    'unit_cost' => 4100.00,
                    'unit_price' => 6000.00,
                    'discount_amount' => 0.00,
                    'total_price' => 24000.00,
                ],
                [
                    'product_id' => $servers->id,
                    'quantity' => 4,
                    'unit_cost' => 3800.00,
                    'unit_price' => 6000.00,
                    'discount_amount' => 0.00,
                    'total_price' => 24000.00,
                ],
            ]);

            Payment::firstOrCreate(['receipt_number' => 'RCT-2026-F91A2'], [
                'sales_order_id' => $so1->id,
                'amount' => 51750.00,
                'payment_method' => 'BANK_TRANSFER',
                'payment_reference' => 'WIRE-SNB-20261109-8819',
                'payment_date' => now()->subDays(2)->toDateString(),
                'notes' => 'Corporate advance 100% wire clearance verified via Saudi National Bank.',
                'received_by_user_id' => $admin->id,
            ]);
        }

        // 3. Direct Sales Order with Partial Payment
        $so2 = SalesOrder::firstOrCreate(['order_number' => 'SO-2026-M2L44'], [
            'quotation_id' => null,
            'store_id' => $storeNorth->id,
            'customer_name' => 'Dr. Faisal Al-Zahrani',
            'customer_email' => 'faisal@kfupm-research.edu.sa',
            'customer_phone' => '+966 54 999 1234',
            'company_name' => 'KFUPM Advanced Computing Institute',
            'billing_address' => 'Academic Zone, Dhahran 31261, Eastern Province, KSA',
            'shipping_address' => 'High Performance Computing Lab, Building 22, Dhahran, KSA',
            'status' => OrderStatus::PROCESSING,
            'payment_status' => PaymentStatus::PARTIAL,
            'payment_method' => 'BANK_TRANSFER',
            'subtotal' => 26500.00,
            'discount_amount' => 1500.00,
            'tax_amount' => 3750.00,
            'total_amount' => 28750.00,
            'paid_amount' => 15000.00,
            'created_by_user_id' => $admin->id,
            'confirmed_by_user_id' => $admin->id,
            'confirmed_at' => now()->subDay(),
            'notes' => 'Direct corporate order for university AI training infrastructure.',
        ]);

        if ($so2->items()->count() === 0 && $storage) {
            $so2->items()->create([
                'product_id' => $storage->id,
                'quantity' => 3,
                'unit_cost' => 6500.00,
                'unit_price' => 8833.33,
                'discount_amount' => 500.00,
                'total_price' => 25000.00,
            ]);

            Payment::firstOrCreate(['receipt_number' => 'RCT-2026-K1098'], [
                'sales_order_id' => $so2->id,
                'amount' => 15000.00,
                'payment_method' => 'BANK_TRANSFER',
                'payment_reference' => 'WIRE-ALINMA-9921',
                'payment_date' => now()->subDay()->toDateString(),
                'notes' => 'Initial 50% mobilization installment.',
                'received_by_user_id' => $admin->id,
            ]);
        }
    }
}
