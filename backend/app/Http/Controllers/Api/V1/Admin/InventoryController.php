<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreInventoryResource;
use App\Models\Product;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * List store-specific inventory records with search, filtering, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = StoreInventory::with(['product.brand', 'product.category', 'product.primaryImage', 'store']);

        // Store isolation / filtering
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->input('store_id'));
        } elseif (!$request->user()->isSuperAdmin() && $request->user()->store_id) {
            // Constrain to user's assigned store if not super admin
            $query->where('store_id', $request->user()->store_id);
        }

        // Product search (name, SKU, model number)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('model_number', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $catId = $request->input('category_id');
            $query->whereHas('product', function ($q) use ($catId) {
                $q->where('category_id', $catId);
            });
        }

        // Brand filter
        if ($request->filled('brand_id')) {
            $brandId = $request->input('brand_id');
            $query->whereHas('product', function ($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            });
        }

        // Stock status filter
        if ($request->filled('status')) {
            $status = strtolower($request->input('status'));
            if ($status === 'out_of_stock') {
                $query->where('quantity', '<=', 0);
            } elseif ($status === 'low_stock') {
                $query->where('quantity', '>', 0)
                    ->whereColumn('quantity', '<=', 'min_alert_quantity');
            } elseif ($status === 'in_stock') {
                $query->whereColumn('quantity', '>', 'min_alert_quantity');
            }
        }

        // Shelf location filter
        if ($request->filled('shelf_location')) {
            $query->where('shelf_location', 'like', '%' . $request->input('shelf_location') . '%');
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        if ($sortBy === 'quantity') {
            $query->orderBy('quantity', $sortOrder);
        } elseif ($sortBy === 'last_movement') {
            $query->orderBy('last_movement_at', $sortOrder);
        } else {
            $query->orderBy('id', $sortOrder);
        }

        $perPage = (int) $request->input('per_page', 15);
        $inventories = $query->paginate($perPage);

        return $this->paginatedResponse(
            StoreInventoryResource::collection($inventories),
            'Store inventory records retrieved successfully.'
        );
    }

    /**
     * Show single inventory record with multi-store comparison and product details.
     */
    public function show(int $id): JsonResponse
    {
        $inventory = StoreInventory::with([
            'product.brand',
            'product.category',
            'product.images',
            'product.specifications',
            'product.inventories.store',
            'store.manager',
        ])->findOrFail($id);

        return $this->successResponse(
            new StoreInventoryResource($inventory),
            'Inventory record retrieved.'
        );
    }

    /**
     * Update minimum stock alert threshold or shelf location.
     */
    public function updateAlert(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'min_alert_quantity' => ['required', 'integer', 'min:0'],
            'shelf_location' => ['nullable', 'string', 'max:100'],
        ]);

        $inventory = StoreInventory::findOrFail($id);
        $inventory->update($validated);

        return $this->successResponse(
            new StoreInventoryResource($inventory->load(['product', 'store'])),
            'Inventory threshold and location updated.'
        );
    }

    /**
     * Inventory Dashboard Metrics
     */
    public function dashboard(Request $request): JsonResponse
    {
        $storeId = $request->input('store_id');
        if (!$request->user()->isSuperAdmin() && $request->user()->store_id) {
            $storeId = $request->user()->store_id;
        }

        $metrics = $this->inventoryService->getDashboardMetrics($storeId ? (int) $storeId : null);

        return $this->successResponse($metrics, 'Inventory dashboard metrics retrieved.');
    }

    /**
     * Inventory Reporting API
     */
    public function reports(Request $request): JsonResponse
    {
        $reportType = $request->input('type', 'current_stock');
        $storeId = $request->input('store_id');

        switch ($reportType) {
            case 'current_stock':
                $data = StoreInventory::with(['product.brand', 'product.category', 'store'])
                    ->when($storeId, fn($q) => $q->where('store_id', $storeId))
                    ->orderBy('store_id')
                    ->orderBy('quantity', 'desc')
                    ->get();
                $resource = StoreInventoryResource::collection($data);
                break;

            case 'low_stock':
                $data = StoreInventory::with(['product.brand', 'product.category', 'store'])
                    ->whereColumn('quantity', '<=', 'min_alert_quantity')
                    ->when($storeId, fn($q) => $q->where('store_id', $storeId))
                    ->orderBy('quantity')
                    ->get();
                $resource = StoreInventoryResource::collection($data);
                break;

            case 'aging':
                // Group inventory by movement age
                $inventories = StoreInventory::with(['product', 'store'])
                    ->when($storeId, fn($q) => $q->where('store_id', $storeId))
                    ->get()
                    ->map(function ($inv) {
                        $lastDate = $inv->last_movement_at ?? $inv->updated_at ?? $inv->created_at;
                        $days = $lastDate ? now()->diffInDays($lastDate) : 0;

                        $bracket = match (true) {
                            $days <= 30 => '0 - 30 Days (Fast Moving)',
                            $days <= 60 => '31 - 60 Days (Normal Moving)',
                            $days <= 90 => '61 - 90 Days (Slow Moving)',
                            default => '90+ Days (Stagnant / Dead Stock)',
                        };

                        return [
                            'inventory_id' => $inv->id,
                            'product_name' => $inv->product?->name,
                            'sku' => $inv->product?->sku,
                            'store_name' => $inv->store?->name,
                            'quantity' => $inv->quantity,
                            'unit_cost' => (float) ($inv->product?->cost_price ?? 0),
                            'valuation' => round($inv->quantity * (float) ($inv->product?->cost_price ?? 0), 2),
                            'days_inactive' => $days,
                            'age_bracket' => $bracket,
                            'last_movement_date' => $lastDate?->format('Y-m-d'),
                        ];
                    });

                return $this->successResponse([
                    'report_type' => 'aging',
                    'generated_at' => now()->toIso8601String(),
                    'brackets_summary' => [
                        'fast_moving' => $inventories->where('days_inactive', '<=', 30)->sum('quantity'),
                        'normal_moving' => $inventories->whereBetween('days_inactive', [31, 60])->sum('quantity'),
                        'slow_moving' => $inventories->whereBetween('days_inactive', [61, 90])->sum('quantity'),
                        'stagnant' => $inventories->where('days_inactive', '>', 90)->sum('quantity'),
                    ],
                    'items' => $inventories->sortByDesc('days_inactive')->values(),
                ], 'Stock aging report generated.');

            default:
                $data = StoreInventory::with(['product', 'store'])->limit(100)->get();
                $resource = StoreInventoryResource::collection($data);
                break;
        }

        return $this->successResponse([
            'report_type' => $reportType,
            'generated_at' => now()->toIso8601String(),
            'total_items' => count($resource),
            'data' => $resource,
        ], 'Inventory report generated.');
    }
}
