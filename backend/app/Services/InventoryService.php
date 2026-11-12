<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Generate unique, collision-proof movement reference number.
     */
    public static function generateMovementNumber(string $prefix = 'MOV'): string
    {
        $year = date('Y');
        do {
            $random = strtoupper(Str::random(5));
            $number = "{$prefix}-{$year}-{$random}";
        } while (StockMovement::where('movement_number', $number)->exists());

        return $number;
    }

    /**
     * 1. Stock Receiving: Process new inventory arriving at a destination store.
     */
    public function receiveStock(array $data, User $user): StockMovement
    {
        return DB::transaction(function () use ($data, $user) {
            $storeId = $data['destination_store_id'];
            $store = Store::findOrFail($storeId);

            $movementNumber = self::generateMovementNumber('RCV');

            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'type' => StockMovementType::RECEIVE,
                'supplier' => $data['supplier'] ?? null,
                'external_reference' => $data['purchase_reference'] ?? ($data['external_reference'] ?? null),
                'status' => 'COMPLETED',
                'source_store_id' => null,
                'destination_store_id' => $storeId,
                'reference_type' => 'PurchaseOrder',
                'user_id' => $user->id,
                'reason' => $data['reason'] ?? 'Goods Receiving from Supplier',
                'notes' => $data['remarks'] ?? ($data['notes'] ?? null),
            ]);

            $items = $data['items'] ?? [
                [
                    'product_id' => $data['product_id'],
                    'quantity' => $data['quantity'],
                    'unit_cost' => $data['unit_cost'] ?? 0.00,
                    'notes' => $data['item_notes'] ?? null,
                ]
            ];

            foreach ($items as $itemData) {
                $productId = $itemData['product_id'];
                $qty = (int) $itemData['quantity'];
                $unitCost = (float) ($itemData['unit_cost'] ?? 0.00);

                if ($qty <= 0) {
                    throw new Exception("Received quantity must be greater than zero for product ID {$productId}.");
                }

                // Pessimistic Lock on StoreInventory
                $inventory = StoreInventory::where('store_id', $storeId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                $previousQty = $inventory ? $inventory->quantity : 0;
                $newQty = $previousQty + $qty;

                if (!$inventory) {
                    $inventory = StoreInventory::create([
                        'store_id' => $storeId,
                        'product_id' => $productId,
                        'quantity' => $newQty,
                        'reserved_quantity' => 0,
                        'min_alert_quantity' => 5,
                        'shelf_location' => $itemData['shelf_location'] ?? null,
                        'last_movement_at' => now(),
                    ]);
                } else {
                    $inventory->update([
                        'quantity' => $newQty,
                        'last_movement_at' => now(),
                    ]);
                }

                StockMovementItem::create([
                    'stock_movement_id' => $movement->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'previous_quantity' => $previousQty,
                    'new_quantity' => $newQty,
                    'unit_cost' => $unitCost,
                    'notes' => $itemData['notes'] ?? null,
                ]);

                // Update product cost_price if provided
                if ($unitCost > 0) {
                    Product::where('id', $productId)->update(['cost_price' => $unitCost]);
                }
            }

            AuditService::log('STOCK_RECEIVED', $movement, null, [
                'movement_number' => $movementNumber,
                'destination_store' => $store->name,
                'items_count' => count($items),
            ]);

            return $movement->load(['items.product', 'destinationStore', 'user']);
        });
    }

    /**
     * 2. Stock Issuing: Deduct inventory from a store for project deployment, RMA, demo, or scrap.
     */
    public function issueStock(array $data, User $user): StockMovement
    {
        return DB::transaction(function () use ($data, $user) {
            $storeId = $data['source_store_id'];
            $store = Store::findOrFail($storeId);

            $movementNumber = self::generateMovementNumber('ISS');

            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'type' => StockMovementType::ISSUE,
                'external_reference' => $data['reference_number'] ?? ($data['external_reference'] ?? null),
                'status' => 'COMPLETED',
                'source_store_id' => $storeId,
                'destination_store_id' => null,
                'reference_type' => 'StockIssue',
                'user_id' => $user->id,
                'reason' => $data['issue_reason'] ?? ($data['reason'] ?? 'Operational Issuance'),
                'notes' => $data['notes'] ?? null,
            ]);

            $items = $data['items'] ?? [
                [
                    'product_id' => $data['product_id'],
                    'quantity' => $data['quantity'],
                    'notes' => $data['item_notes'] ?? null,
                ]
            ];

            foreach ($items as $itemData) {
                $productId = $itemData['product_id'];
                $qty = (int) $itemData['quantity'];
                $product = Product::findOrFail($productId);

                if ($qty <= 0) {
                    throw new Exception("Issued quantity must be greater than zero for {$product->name}.");
                }

                // Pessimistic Lock on StoreInventory
                $inventory = StoreInventory::where('store_id', $storeId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$inventory || $inventory->availableQuantity() < $qty) {
                    $available = $inventory ? $inventory->availableQuantity() : 0;
                    throw new Exception(
                        "Insufficient stock for '{$product->name}' (SKU: {$product->sku}) at {$store->name}. Available: {$available}, requested: {$qty}. Negative stock is strictly prohibited."
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
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'previous_quantity' => $previousQty,
                    'new_quantity' => $newQty,
                    'unit_cost' => (float) $product->cost_price,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            AuditService::log('STOCK_ISSUED', $movement, null, [
                'movement_number' => $movementNumber,
                'source_store' => $store->name,
                'reason' => $movement->reason,
                'items_count' => count($items),
            ]);

            return $movement->load(['items.product', 'sourceStore', 'user']);
        });
    }

    /**
     * 3. Inter-Store Stock Transfers: Atomic transfer from source store to destination store.
     */
    public function transferStock(array $data, User $user): StockMovement
    {
        return DB::transaction(function () use ($data, $user) {
            $sourceStoreId = $data['source_store_id'];
            $destStoreId = $data['destination_store_id'];

            if ($sourceStoreId == $destStoreId) {
                throw new Exception('Source store and destination store must be different branches.');
            }

            $sourceStore = Store::findOrFail($sourceStoreId);
            $destStore = Store::findOrFail($destStoreId);

            $movementNumber = !empty($data['transfer_reference'])
                ? $data['transfer_reference']
                : self::generateMovementNumber('TRF');

            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'type' => StockMovementType::TRANSFER_OUT,
                'external_reference' => $data['transfer_reference'] ?? null,
                'status' => 'COMPLETED',
                'source_store_id' => $sourceStoreId,
                'destination_store_id' => $destStoreId,
                'reference_type' => 'InterStoreTransfer',
                'user_id' => $user->id,
                'reason' => $data['reason'] ?? "Regional Stock Rebalancing from {$sourceStore->name} to {$destStore->name}",
                'notes' => $data['transfer_notes'] ?? ($data['notes'] ?? null),
            ]);

            $items = $data['items'] ?? [
                [
                    'product_id' => $data['product_id'],
                    'quantity' => $data['quantity'],
                    'notes' => $data['item_notes'] ?? null,
                ]
            ];

            foreach ($items as $itemData) {
                $productId = $itemData['product_id'];
                $qty = (int) $itemData['quantity'];
                $product = Product::findOrFail($productId);

                if ($qty <= 0) {
                    throw new Exception("Transfer quantity must be greater than zero for {$product->name}.");
                }

                // 1. Lock and validate Source Inventory
                $sourceInv = StoreInventory::where('store_id', $sourceStoreId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$sourceInv || $sourceInv->availableQuantity() < $qty) {
                    $avail = $sourceInv ? $sourceInv->availableQuantity() : 0;
                    throw new Exception(
                        "Cannot transfer '{$product->name}'. Insufficient available stock at {$sourceStore->name} (Available: {$avail}, Requested: {$qty})."
                    );
                }

                $sourcePrev = $sourceInv->quantity;
                $sourceNew = $sourcePrev - $qty;

                $sourceInv->update([
                    'quantity' => $sourceNew,
                    'last_movement_at' => now(),
                ]);

                // 2. Lock and credit Destination Inventory
                $destInv = StoreInventory::where('store_id', $destStoreId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                $destPrev = $destInv ? $destInv->quantity : 0;
                $destNew = $destPrev + $qty;

                if (!$destInv) {
                    StoreInventory::create([
                        'store_id' => $destStoreId,
                        'product_id' => $productId,
                        'quantity' => $destNew,
                        'reserved_quantity' => 0,
                        'min_alert_quantity' => 5,
                        'shelf_location' => null,
                        'last_movement_at' => now(),
                    ]);
                } else {
                    $destInv->update([
                        'quantity' => $destNew,
                        'last_movement_at' => now(),
                    ]);
                }

                // 3. Record Movement Item
                StockMovementItem::create([
                    'stock_movement_id' => $movement->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'previous_quantity' => $sourcePrev,
                    'new_quantity' => $sourceNew,
                    'unit_cost' => (float) $product->cost_price,
                    'notes' => "Transferred {$qty} units from {$sourceStore->code} to {$destStore->code}",
                ]);
            }

            AuditService::log('STOCK_TRANSFERRED', $movement, null, [
                'movement_number' => $movementNumber,
                'source_store' => $sourceStore->name,
                'destination_store' => $destStore->name,
                'items_count' => count($items),
            ]);

            return $movement->load(['items.product', 'sourceStore', 'destinationStore', 'user']);
        });
    }

    /**
     * 4. Stock Adjustments: Physical audit reconciliation with mandatory reason.
     */
    public function adjustStock(array $data, User $user): StockMovement
    {
        return DB::transaction(function () use ($data, $user) {
            $storeId = $data['store_id'];
            $productId = $data['product_id'];
            $newQuantity = (int) $data['new_quantity'];
            $reason = trim($data['adjustment_reason'] ?? ($data['reason'] ?? ''));

            if (empty($reason)) {
                throw new Exception('An explicit adjustment reason is mandatory for inventory audit compliance.');
            }

            if ($newQuantity < 0) {
                throw new Exception('Stock quantity cannot be adjusted to a negative value.');
            }

            $store = Store::findOrFail($storeId);
            $product = Product::findOrFail($productId);

            // Lock inventory row
            $inventory = StoreInventory::where('store_id', $storeId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            $previousQuantity = $inventory ? $inventory->quantity : 0;
            $quantityDiff = $newQuantity - $previousQuantity;

            if ($quantityDiff === 0) {
                throw new Exception('New quantity is identical to existing quantity. No adjustment needed.');
            }

            if (!$inventory) {
                $inventory = StoreInventory::create([
                    'store_id' => $storeId,
                    'product_id' => $productId,
                    'quantity' => $newQuantity,
                    'reserved_quantity' => 0,
                    'min_alert_quantity' => 5,
                    'shelf_location' => null,
                    'last_movement_at' => now(),
                ]);
            } else {
                $inventory->update([
                    'quantity' => $newQuantity,
                    'last_movement_at' => now(),
                ]);
            }

            $movementNumber = self::generateMovementNumber('ADJ');

            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'type' => StockMovementType::ADJUSTMENT,
                'external_reference' => $data['external_reference'] ?? null,
                'status' => 'COMPLETED',
                'source_store_id' => $storeId,
                'destination_store_id' => null,
                'reference_type' => 'StockAuditAdjustment',
                'user_id' => $user->id,
                'reason' => $reason,
                'notes' => "Physical count adjustment: {$previousQuantity} -> {$newQuantity} (Delta: " . ($quantityDiff > 0 ? "+{$quantityDiff}" : "{$quantityDiff}") . ")",
            ]);

            StockMovementItem::create([
                'stock_movement_id' => $movement->id,
                'product_id' => $productId,
                'quantity' => $quantityDiff,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $newQuantity,
                'unit_cost' => (float) $product->cost_price,
                'notes' => $data['notes'] ?? null,
            ]);

            AuditService::log('STOCK_ADJUSTED', $movement, [
                'previous_quantity' => $previousQuantity
            ], [
                'movement_number' => $movementNumber,
                'store' => $store->name,
                'product' => $product->name,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $newQuantity,
                'difference' => $quantityDiff,
                'reason' => $reason,
            ]);

            return $movement->load(['items.product', 'sourceStore', 'user']);
        });
    }

    /**
     * 5. Inventory Dashboard Metrics
     */
    public function getDashboardMetrics(?int $storeId = null): array
    {
        $query = StoreInventory::query();
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        $totalProducts = Product::count();
        $totalStockQty = (int) $query->sum('quantity');

        // Total Stock Value ($) = sum(store_inventories.quantity * products.cost_price)
        $totalValue = (float) DB::table('store_inventories')
            ->join('products', 'store_inventories.product_id', '=', 'products.id')
            ->when($storeId, fn($q) => $q->where('store_inventories.store_id', $storeId))
            ->sum(DB::raw('store_inventories.quantity * products.cost_price'));

        $lowStockCount = (int) (clone $query)
            ->where('quantity', '>', 0)
            ->whereColumn('quantity', '<=', 'min_alert_quantity')
            ->count();

        $outOfStockCount = (int) (clone $query)
            ->where('quantity', '<=', 0)
            ->count();

        $inStockCount = (int) (clone $query)
            ->whereColumn('quantity', '>', 'min_alert_quantity')
            ->count();

        // Store-wise distribution
        $storeDistribution = Store::withCount('inventories')
            ->get()
            ->map(function ($s) {
                $qty = (int) $s->inventories()->sum('quantity');
                $val = (float) DB::table('store_inventories')
                    ->join('products', 'store_inventories.product_id', '=', 'products.id')
                    ->where('store_inventories.store_id', $s->id)
                    ->sum(DB::raw('store_inventories.quantity * products.cost_price'));

                return [
                    'store_id' => $s->id,
                    'store_name' => $s->name,
                    'store_code' => $s->code,
                    'is_primary' => $s->is_primary,
                    'total_quantity' => $qty,
                    'total_valuation' => $val,
                    'low_stock_count' => $s->lowStockCount(),
                    'out_of_stock_count' => $s->outOfStockCount(),
                ];
            });

        // Recent Movements
        $recentMovements = StockMovement::with(['items.product', 'sourceStore', 'destinationStore', 'user'])
            ->when($storeId, function ($q) use ($storeId) {
                $q->where('source_store_id', $storeId)->orWhere('destination_store_id', $storeId);
            })
            ->latest()
            ->limit(8)
            ->get();

        return [
            'total_products' => $totalProducts,
            'total_stock_quantity' => $totalStockQty,
            'total_stock_value' => round($totalValue, 2),
            'in_stock_count' => $inStockCount,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'store_distribution' => $storeDistribution,
            'recent_movements' => $recentMovements,
        ];
    }
}
