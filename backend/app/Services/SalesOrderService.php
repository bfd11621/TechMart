<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\QuotationStatus;
use App\Enums\RfqStatus;
use App\Enums\StockMovementType;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesOrderService
{
    /**
     * Generate unique sales order reference number: SO-YYYY-XXXXX
     */
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        do {
            $random = strtoupper(Str::random(5));
            $number = "SO-{$year}-{$random}";
        } while (SalesOrder::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Generate unique payment receipt number: RCT-YYYY-XXXXX
     */
    public static function generateReceiptNumber(): string
    {
        $year = date('Y');
        do {
            $random = strtoupper(Str::random(5));
            $number = "RCT-{$year}-{$random}";
        } while (Payment::where('receipt_number', $number)->exists());

        return $number;
    }

    /**
     * 1. Convert Approved Quotation into a Confirmed Sales Order.
     * Enforces:
     * - Only APPROVED quotations can be converted.
     * - Duplicate conversion prevention.
     * - Atomic stock deduction with lockForUpdate().
     * - Zero negative stock rule.
     * - Revenue recognized strictly upon order confirmation.
     */
    public function convertQuotationToOrder(Quotation $quotation, array $data, User $user): SalesOrder
    {
        return DB::transaction(function () use ($quotation, $data, $user) {
            // Lock Quotation to prevent concurrent race conditions
            $lockedQuotation = Quotation::where('id', $quotation->id)->lockForUpdate()->first();

            // 1. Validation: Must be approved
            if ($lockedQuotation->status !== QuotationStatus::APPROVED) {
                throw new Exception(
                    "Quotation '{$lockedQuotation->quotation_number}' cannot be converted. Status must be APPROVED (Current status: {$lockedQuotation->status->label()})."
                );
            }

            // 2. Validation: Prevent duplicate conversion
            if (!is_null($lockedQuotation->converted_at) || $lockedQuotation->salesOrder()->exists()) {
                $existingOrder = $lockedQuotation->salesOrder;
                $orderNum = $existingOrder ? $existingOrder->order_number : 'Existing Order';
                throw new Exception(
                    "Duplicate conversion prevented. Quotation '{$lockedQuotation->quotation_number}' has already been converted to Sales Order #{$orderNum}."
                );
            }

            $storeId = $data['store_id'] ?? $lockedQuotation->store_id;
            $store = Store::findOrFail($storeId);

            $orderNumber = self::generateOrderNumber();

            // 3. Create Sales Order
            $order = SalesOrder::create([
                'order_number' => $orderNumber,
                'quotation_id' => $lockedQuotation->id,
                'store_id' => $storeId,
                'customer_name' => $lockedQuotation->customer_name,
                'customer_email' => $lockedQuotation->customer_email,
                'customer_phone' => $lockedQuotation->customer_phone,
                'company_name' => $lockedQuotation->company_name,
                'billing_address' => $lockedQuotation->billing_address,
                'shipping_address' => $data['shipping_address'] ?? ($lockedQuotation->billing_address ?? "Dispatch from {$store->name}"),
                'status' => OrderStatus::CONFIRMED, // Confirmed revenue active
                'payment_status' => PaymentStatus::UNPAID,
                'payment_method' => $data['payment_method'] ?? 'Bank Wire / Purchase Order',
                'subtotal' => $lockedQuotation->subtotal,
                'discount_amount' => $lockedQuotation->discount_amount,
                'tax_amount' => $lockedQuotation->tax_amount,
                'total_amount' => $lockedQuotation->total_amount,
                'paid_amount' => 0.00,
                'created_by_user_id' => $user->id,
                'confirmed_by_user_id' => $user->id,
                'confirmed_at' => now(),
                'notes' => $data['notes'] ?? "Confirmed sales order converted from accepted quotation #{$lockedQuotation->quotation_number}.",
            ]);

            // 4. Create Movement Record for Inventory Audit
            $movementNumber = InventoryService::generateMovementNumber('SAL');

            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'type' => StockMovementType::SALE_DEDUCTION,
                'external_reference' => $orderNumber,
                'status' => 'COMPLETED',
                'source_store_id' => $storeId,
                'destination_store_id' => null,
                'reference_type' => 'SalesOrder',
                'reference_id' => $order->id,
                'user_id' => $user->id,
                'reason' => "Sales fulfillment stock allocation for Order #{$orderNumber}",
                'notes' => "Automatic deduction upon confirmed order conversion from quote #{$lockedQuotation->quotation_number}.",
            ]);

            // 5. Transfer Items & Execute Atomic Stock Deduction
            foreach ($lockedQuotation->items as $qItem) {
                $product = $qItem->product;
                $qty = (int) $qItem->quantity;

                // Create Order Item
                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_cost' => (float) ($product->cost_price ?? 0.00),
                    'unit_price' => (float) $qItem->unit_price,
                    'discount_amount' => round($qty * $qItem->unit_price * ($qItem->discount_percent / 100), 2),
                    'total_price' => (float) $qItem->total_price,
                ]);

                // Pessimistic Lock on Store Inventory
                $inventory = StoreInventory::where('store_id', $storeId)
                    ->where('product_id', $product->id)
                    ->lockForUpdate()
                    ->first();

                $available = $inventory ? $inventory->availableQuantity() : 0;

                if (!$inventory || $available < $qty) {
                    throw new Exception(
                        "Stock deduction failed: Insufficient on-hand stock for '{$product->name}' (SKU: {$product->sku}) at {$store->name}. Available: {$available}, Required: {$qty}. Conversion cancelled to protect inventory integrity."
                    );
                }

                $previousQty = $inventory->quantity;
                $newQty = $previousQty - $qty;

                $inventory->update([
                    'quantity' => $newQty,
                    'last_movement_at' => now(),
                ]);

                // Record Movement Item
                StockMovementItem::create([
                    'stock_movement_id' => $movement->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'previous_quantity' => $previousQty,
                    'new_quantity' => $newQty,
                    'unit_cost' => (float) $product->cost_price,
                    'notes' => "Sold via Sales Order #{$orderNumber}",
                ]);
            }

            // 6. Update Quotation Status to CONVERTED
            $lockedQuotation->update(['converted_at' => now()]);
            $lockedQuotation->logStatusChange(
                QuotationStatus::CONVERTED,
                $user,
                "Quotation converted into confirmed Sales Order #{$orderNumber}."
            );

            // 7. If linked to customer RFQ, update RFQ status to CONVERTED_TO_ORDER
            if ($lockedQuotation->rfqRequest) {
                $lockedQuotation->rfqRequest->logStatusChange(
                    RfqStatus::CONVERTED_TO_ORDER,
                    $user,
                    "Quotation inquiry fulfilled into confirmed Sales Order #{$orderNumber}."
                );
            }

            AuditService::log('SALES_ORDER_CONFIRMED', $order, null, [
                'order_number' => $orderNumber,
                'quotation_number' => $lockedQuotation->quotation_number,
                'customer_name' => $order->customer_name,
                'total_amount' => $order->total_amount,
                'store' => $store->name,
            ]);

            return $order->load(['items.product', 'store', 'createdBy', 'confirmedBy']);
        });
    }

    /**
     * 2. Direct Sales Registration (Counter Sale or Direct Contract).
     */
    public function createDirectSale(array $data, User $user): SalesOrder
    {
        return DB::transaction(function () use ($data, $user) {
            $storeId = $data['store_id'] ?? ($user->store_id ?? Store::where('is_primary', true)->value('id'));
            $store = Store::findOrFail($storeId);

            $orderNumber = self::generateOrderNumber();
            $subtotal = 0.00;
            $itemsProcessed = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];
                $unitPrice = (float) ($item['unit_price'] ?? $product->base_price);
                $discountPercent = (float) ($item['discount_percent'] ?? 0.00);
                $discountAmount = round($qty * $unitPrice * ($discountPercent / 100), 2);
                $lineTotal = round(($qty * $unitPrice) - $discountAmount, 2);

                $subtotal += $lineTotal;

                $itemsProcessed[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'unit_cost' => (float) $product->cost_price,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $discountAmount,
                    'total_price' => $lineTotal,
                ];
            }

            $discountAmount = (float) ($data['discount_amount'] ?? 0.00);
            $taxRate = (float) ($data['tax_rate'] ?? 15.00);
            $discountedSubtotal = max(0.00, $subtotal - $discountAmount);
            $taxAmount = round($discountedSubtotal * ($taxRate / 100), 2);
            $totalAmount = round($discountedSubtotal + $taxAmount, 2);

            $order = SalesOrder::create([
                'order_number' => $orderNumber,
                'quotation_id' => null,
                'store_id' => $storeId,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? '',
                'company_name' => $data['company_name'] ?? null,
                'billing_address' => $data['billing_address'] ?? null,
                'shipping_address' => $data['shipping_address'] ?? null,
                'status' => OrderStatus::CONFIRMED,
                'payment_status' => PaymentStatus::UNPAID,
                'payment_method' => $data['payment_method'] ?? 'Bank Wire',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0.00,
                'created_by_user_id' => $user->id,
                'confirmed_by_user_id' => $user->id,
                'confirmed_at' => now(),
                'notes' => $data['notes'] ?? 'Direct confirmed contract sale.',
            ]);

            // Stock Movement Deduction
            $movementNumber = InventoryService::generateMovementNumber('SAL');

            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'type' => StockMovementType::SALE_DEDUCTION,
                'external_reference' => $orderNumber,
                'status' => 'COMPLETED',
                'source_store_id' => $storeId,
                'destination_store_id' => null,
                'reference_type' => 'SalesOrder',
                'reference_id' => $order->id,
                'user_id' => $user->id,
                'reason' => "Direct Sales order fulfillment #{$orderNumber}",
                'notes' => "Direct inventory deduction at {$store->name}",
            ]);

            foreach ($itemsProcessed as $proc) {
                $product = $proc['product'];
                $qty = $proc['quantity'];

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_cost' => $proc['unit_cost'],
                    'unit_price' => $proc['unit_price'],
                    'discount_amount' => $proc['discount_amount'],
                    'total_price' => $proc['total_price'],
                ]);

                // Inventory Deduction
                $inventory = StoreInventory::where('store_id', $storeId)
                    ->where('product_id', $product->id)
                    ->lockForUpdate()
                    ->first();

                $available = $inventory ? $inventory->availableQuantity() : 0;
                if (!$inventory || $available < $qty) {
                    throw new Exception(
                        "Insufficient stock for '{$product->name}' at {$store->name}. Available: {$available}, required: {$qty}."
                    );
                }

                $previousQty = $inventory->quantity;
                $newQty = $previousQty - $qty;

                $inventory->update([
                    'quantity' => $newQty,
                    'last_movement_at' => now(),
                ]);

                StockMovementItem::create([
                    'stock_movement_id' => $movement->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'previous_quantity' => $previousQty,
                    'new_quantity' => $newQty,
                    'unit_cost' => $proc['unit_cost'],
                ]);
            }

            AuditService::log('DIRECT_SALE_CONFIRMED', $order, null, [
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
            ]);

            return $order->load(['items.product', 'store', 'createdBy']);
        });
    }

    /**
     * 3. Record Payment against a Sales Order.
     */
    public function recordPayment(SalesOrder $order, array $data, User $user): Payment
    {
        return DB::transaction(function () use ($order, $data, $user) {
            $amount = (float) $data['amount'];
            if ($amount <= 0) {
                throw new Exception('Payment amount must be greater than zero.');
            }

            $receiptNumber = self::generateReceiptNumber();

            $payment = Payment::create([
                'sales_order_id' => $order->id,
                'receipt_number' => $receiptNumber,
                'amount' => $amount,
                'payment_method' => $data['payment_method'] ?? 'Bank Wire Transfer',
                'payment_reference' => $data['payment_reference'] ?? null,
                'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                'notes' => $data['notes'] ?? null,
                'received_by_user_id' => $user->id,
            ]);

            $newPaidAmount = round((float) $order->paid_amount + $amount, 2);
            $totalAmount = (float) $order->total_amount;

            $newPaymentStatus = PaymentStatus::PARTIAL;
            if ($newPaidAmount >= $totalAmount) {
                $newPaymentStatus = PaymentStatus::PAID;
            }

            $order->update([
                'paid_amount' => $newPaidAmount,
                'payment_status' => $newPaymentStatus,
            ]);

            AuditService::log('PAYMENT_RECORDED', $order, null, [
                'receipt_number' => $receiptNumber,
                'amount' => $amount,
                'new_paid_total' => $newPaidAmount,
                'payment_status' => $newPaymentStatus->value,
            ]);

            return $payment->load(['salesOrder', 'receivedBy']);
        });
    }

    /**
     * 4. Sales Dashboard Metrics strictly respecting revenue rules:
     * - RFQs and Quotes are NEVER revenue.
     * - Only CONFIRMED, PROCESSING, COMPLETED orders are counted.
     * - Cancelled orders are excluded from revenue.
     */
    public function getDashboardMetrics(?int $storeId = null): array
    {
        $today = now()->toDateString();
        $startOfWeek = now()->startOfWeek()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();

        $baseOrders = SalesOrder::query()
            ->when($storeId, fn($q) => $q->where('store_id', $storeId));

        // Confirmed revenue scope
        $revenueOrders = (clone $baseOrders)->whereIn('status', [
            OrderStatus::CONFIRMED->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::COMPLETED->value,
        ]);

        $totalSales = (float) (clone $revenueOrders)->sum('total_amount');
        $dailySales = (float) (clone $revenueOrders)->whereDate('confirmed_at', $today)->sum('total_amount');
        $weeklySales = (float) (clone $revenueOrders)->whereDate('confirmed_at', '>=', $startOfWeek)->sum('total_amount');
        $monthlySales = (float) (clone $revenueOrders)->whereDate('confirmed_at', '>=', $startOfMonth)->sum('total_amount');

        // Total physical units sold across confirmed orders
        $totalProductsSold = (int) DB::table('sales_order_items')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->whereIn('sales_orders.status', [
                OrderStatus::CONFIRMED->value,
                OrderStatus::PROCESSING->value,
                OrderStatus::COMPLETED->value,
            ])
            ->when($storeId, fn($q) => $q->where('sales_orders.store_id', $storeId))
            ->sum('sales_order_items.quantity');

        // Order counts by operational state
        $pendingOrdersCount = (clone $baseOrders)->where('status', OrderStatus::PENDING->value)->count();
        $completedOrdersCount = (clone $baseOrders)->where('status', OrderStatus::COMPLETED->value)->count();

        // Outstanding payment amounts across confirmed orders
        $totalConfirmedAmount = (float) (clone $revenueOrders)->sum('total_amount');
        $totalPaidAmount = (float) (clone $revenueOrders)->sum('paid_amount');
        $outstandingAmount = max(0.00, $totalConfirmedAmount - $totalPaidAmount);

        // Recent 8 Sales Orders
        $recentOrders = SalesOrder::with(['items.product', 'store', 'confirmedBy'])
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->latest('confirmed_at')
            ->limit(8)
            ->get();

        return [
            'total_sales' => round($totalSales, 2),
            'daily_sales' => round($dailySales, 2),
            'weekly_sales' => round($weeklySales, 2),
            'monthly_sales' => round($monthlySales, 2),
            'total_products_sold' => $totalProductsSold,
            'pending_orders' => $pendingOrdersCount,
            'completed_orders' => $completedOrdersCount,
            'outstanding_amount' => round($outstandingAmount, 2),
            'recent_orders' => $recentOrders,
        ];
    }
}
