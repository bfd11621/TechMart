<?php

namespace App\Services;

use App\Enums\QuotationStatus;
use App\Enums\RfqStatus;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\RfqRequest;
use App\Models\Store;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuotationService
{
    /**
     * Generate unique quotation number: QT-YYYY-XXXXX
     */
    public static function generateQuotationNumber(): string
    {
        $year = date('Y');
        do {
            $random = strtoupper(Str::random(5));
            $number = "QT-{$year}-{$random}";
        } while (Quotation::where('quotation_number', $number)->exists());

        return $number;
    }

    /**
     * 1. Create a professional B2B quotation (from scratch or from an existing RFQ).
     */
    public function createQuotation(array $data, User $user): Quotation
    {
        return DB::transaction(function () use ($data, $user) {
            $storeId = $data['store_id'] ?? ($user->store_id ?? Store::where('is_primary', true)->value('id'));
            $rfqId = $data['rfq_request_id'] ?? null;

            $quotationNumber = self::generateQuotationNumber();

            // Calculate Subtotal and Item Totals
            $subtotal = 0.00;
            $itemsProcessed = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];
                $unitPrice = isset($item['unit_price']) && (float) $item['unit_price'] > 0
                    ? (float) $item['unit_price']
                    : (float) $product->base_price;

                $discountPercent = (float) ($item['discount_percent'] ?? 0.00);
                $lineTotal = round($qty * $unitPrice * (1 - ($discountPercent / 100)), 2);

                $subtotal += $lineTotal;

                $itemsProcessed[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'discount_percent' => $discountPercent,
                    'total_price' => $lineTotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            // Commercial Quotation-level Discount
            $discountType = $data['discount_type'] ?? 'fixed';
            $discountValue = (float) ($data['discount_value'] ?? 0.00);
            $discountAmount = 0.00;

            if ($discountType === 'percentage') {
                $discountAmount = round($subtotal * ($discountValue / 100), 2);
            } else {
                $discountAmount = min($subtotal, $discountValue);
            }

            $discountedSubtotal = max(0.00, $subtotal - $discountAmount);

            // Applicable Regional Tax (e.g. 15% VAT)
            $taxRate = (float) ($data['tax_rate'] ?? 15.00);
            $taxAmount = round($discountedSubtotal * ($taxRate / 100), 2);

            // Final Total
            $totalAmount = round($discountedSubtotal + $taxAmount, 2);

            // Validity Date (defaults to +30 days price lock)
            $validUntil = $data['valid_until'] ?? now()->addDays(30)->toDateString();

            $initialStatus = !empty($data['status'])
                ? (QuotationStatus::tryFrom(strtoupper($data['status'])) ?? QuotationStatus::PREPARED)
                : QuotationStatus::PREPARED;

            $quotation = Quotation::create([
                'quotation_number' => $quotationNumber,
                'rfq_request_id' => $rfqId,
                'store_id' => $storeId,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? '',
                'company_name' => $data['company_name'] ?? null,
                'billing_address' => $data['billing_address'] ?? null,
                'status' => $initialStatus,
                'valid_until' => $validUntil,
                'subtotal' => $subtotal,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'terms_conditions' => $data['terms_conditions'] ?? self::defaultTermsConditions(),
                'notes' => $data['notes'] ?? null,
                'client_notes' => $data['client_notes'] ?? null,
                'prepared_by_user_id' => $user->id,
            ]);

            foreach ($itemsProcessed as $itemRecord) {
                $quotation->items()->create($itemRecord);
            }

            // Log Initial Status History
            $quotation->statusHistories()->create([
                'status' => $initialStatus->value,
                'previous_status' => null,
                'user_id' => $user->id,
                'notes' => $rfqId
                    ? "Official quotation generated from customer RFQ inquiry #{$rfqId}."
                    : 'Official quotation prepared directly by sales solutions engineer.',
            ]);

            // If generated from RFQ, update RFQ status
            if ($rfqId) {
                $rfq = RfqRequest::find($rfqId);
                if ($rfq) {
                    $rfq->logStatusChange(
                        RfqStatus::QUOTATION_PREPARED,
                        $user,
                        "Official proposal #{$quotationNumber} prepared."
                    );
                }
            }

            AuditService::log('QUOTATION_CREATED', $quotation, null, [
                'quotation_number' => $quotationNumber,
                'customer_name' => $quotation->customer_name,
                'total_amount' => $totalAmount,
            ]);

            return $quotation->load(['items.product', 'store', 'preparedBy', 'statusHistories']);
        });
    }

    /**
     * 2. Update quotation line items, pricing, discounts, and validity.
     */
    public function updateQuotation(Quotation $quotation, array $data, User $user): Quotation
    {
        return DB::transaction(function () use ($quotation, $data, $user) {
            if ($quotation->status === QuotationStatus::CONVERTED) {
                throw new Exception('Cannot modify a quotation that has already been converted into a confirmed Sales Order.');
            }

            $subtotal = 0.00;
            $itemsProcessed = [];

            if (!empty($data['items'])) {
                // Delete existing and rebuild items
                $quotation->items()->delete();

                foreach ($data['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $qty = (int) $item['quantity'];
                    $unitPrice = (float) ($item['unit_price'] ?? $product->base_price);
                    $discountPercent = (float) ($item['discount_percent'] ?? 0.00);
                    $lineTotal = round($qty * $unitPrice * (1 - ($discountPercent / 100)), 2);

                    $subtotal += $lineTotal;

                    $itemsProcessed[] = [
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'discount_percent' => $discountPercent,
                        'total_price' => $lineTotal,
                        'notes' => $item['notes'] ?? null,
                    ];
                }

                foreach ($itemsProcessed as $itemRecord) {
                    $quotation->items()->create($itemRecord);
                }
            } else {
                $subtotal = (float) $quotation->items()->sum('total_price');
            }

            $discountType = $data['discount_type'] ?? $quotation->discount_type;
            $discountValue = isset($data['discount_value']) ? (float) $data['discount_value'] : (float) $quotation->discount_value;
            $discountAmount = 0.00;

            if ($discountType === 'percentage') {
                $discountAmount = round($subtotal * ($discountValue / 100), 2);
            } else {
                $discountAmount = min($subtotal, $discountValue);
            }

            $discountedSubtotal = max(0.00, $subtotal - $discountAmount);
            $taxRate = isset($data['tax_rate']) ? (float) $data['tax_rate'] : (float) $quotation->tax_rate;
            $taxAmount = round($discountedSubtotal * ($taxRate / 100), 2);
            $totalAmount = round($discountedSubtotal + $taxAmount, 2);

            $quotation->update([
                'store_id' => $data['store_id'] ?? $quotation->store_id,
                'customer_name' => $data['customer_name'] ?? $quotation->customer_name,
                'customer_email' => $data['customer_email'] ?? $quotation->customer_email,
                'customer_phone' => $data['customer_phone'] ?? $quotation->customer_phone,
                'company_name' => $data['company_name'] ?? $quotation->company_name,
                'billing_address' => $data['billing_address'] ?? $quotation->billing_address,
                'valid_until' => $data['valid_until'] ?? $quotation->valid_until,
                'subtotal' => $subtotal,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'terms_conditions' => $data['terms_conditions'] ?? $quotation->terms_conditions,
                'notes' => $data['notes'] ?? $quotation->notes,
                'client_notes' => $data['client_notes'] ?? $quotation->client_notes,
            ]);

            AuditService::log('QUOTATION_UPDATED', $quotation, null, [
                'quotation_number' => $quotation->quotation_number,
                'total_amount' => $totalAmount,
            ]);

            return $quotation->fresh(['items.product', 'store', 'preparedBy', 'statusHistories']);
        });
    }

    /**
     * 3. Transition Quotation Status and log history.
     */
    public function transitionStatus(Quotation $quotation, string $newStatus, ?string $notes, User $user): Quotation
    {
        $statusEnum = QuotationStatus::tryFrom(strtoupper($newStatus));
        if (!$statusEnum) {
            throw new Exception("Invalid quotation status: {$newStatus}");
        }

        if ($quotation->status === QuotationStatus::CONVERTED) {
            throw new Exception('Cannot transition a quotation that has already been converted into a Sales Order.');
        }

        $quotation->logStatusChange($statusEnum, $user, $notes);

        if ($statusEnum === QuotationStatus::APPROVED) {
            $quotation->update(['approved_at' => now()]);
            // Also notify or sync RFQ if linked
            if ($quotation->rfqRequest) {
                $quotation->rfqRequest->logStatusChange(
                    RfqStatus::APPROVED,
                    $user,
                    "Client officially approved quotation #{$quotation->quotation_number}."
                );
            }
        } elseif ($statusEnum === QuotationStatus::SENT) {
            if ($quotation->rfqRequest) {
                $quotation->rfqRequest->logStatusChange(
                    RfqStatus::SENT_TO_CUSTOMER,
                    $user,
                    "Quotation #{$quotation->quotation_number} dispatched to client."
                );
            }
        } elseif ($statusEnum === QuotationStatus::REJECTED) {
            if ($quotation->rfqRequest) {
                $quotation->rfqRequest->logStatusChange(
                    RfqStatus::REJECTED,
                    $user,
                    "Client rejected quotation proposal."
                );
            }
        }

        AuditService::log('QUOTATION_STATUS_CHANGED', $quotation, null, [
            'quotation_number' => $quotation->quotation_number,
            'new_status' => $statusEnum->value,
            'notes' => $notes,
        ]);

        return $quotation->fresh(['statusHistories.user']);
    }

    /**
     * 4. Server-Side PDF Generation using DomPDF.
     */
    public function generatePdf(Quotation $quotation)
    {
        $quotation->load(['items.product.brand', 'store.manager', 'preparedBy']);

        $pdf = Pdf::loadView('pdf.quotation', [
            'quotation' => $quotation,
            'company' => [
                'name' => 'iRACK IT Solution',
                'wordmark' => 'iRACK TECH-MART',
                'tagline' => 'Enterprise Hardware Infrastructure & Turnkey Systems Integration',
                'headquarters' => '100 Technology Parkway, Suite 400, Silicon Valley, CA 94016',
                'phone' => '+1 (555) 019-2831 / +1 (800) 472-2548',
                'email' => 'procurement@irack.com / sales@irack.com',
                'website' => 'https://www.irack.com',
                'tax_id' => 'US-EIN-94-3829104',
            ],
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Default B2B Commercial Terms & Conditions
     */
    public static function defaultTermsConditions(): string
    {
        return "1. Price Validity: All quoted unit prices and commercial discounts are locked for 30 calendar days from the date of quotation issuance.\n" .
               "2. Payment Terms: 50% advance upon confirmed Sales Order issuance; 50% net 30 days upon delivery and client commissioning.\n" .
               "3. Delivery SLA: Standard items dispatched within 24-48 business hours from nearest regional fulfillment hub. Customized configure-to-order (CTO) server nodes require 7-10 business days.\n" .
               "4. Warranty & Support: 100% genuine OEM hardware covered under manufacturer warranty (Dell ProSupport, Cisco SmartNet, Schneider Electric SLA).\n" .
               "5. Acceptance: To accept this quotation, please sign below or confirm via your corporate procurement portal.";
    }
}
