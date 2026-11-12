<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockAdjustmentRequest;
use App\Http\Requests\Admin\StockIssuingRequest;
use App\Http\Requests\Admin\StockReceivingRequest;
use App\Http\Requests\Admin\StockTransferRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * List stock movement transactions with search, type filter, store filter, and date range.
     */
    public function index(Request $request): JsonResponse
    {
        $query = StockMovement::with(['items.product', 'sourceStore', 'destinationStore', 'user']);

        // Type filter (RECEIVE, ISSUE, TRANSFER_OUT, TRANSFER_IN, ADJUSTMENT)
        if ($request->filled('type')) {
            $query->where('type', strtoupper($request->input('type')));
        }

        // Store filter (source or destination)
        if ($request->filled('store_id')) {
            $storeId = $request->input('store_id');
            $query->where(function ($q) use ($storeId) {
                $q->where('source_store_id', $storeId)
                  ->orWhere('destination_store_id', $storeId);
            });
        } elseif (!$request->user()->isSuperAdmin() && $request->user()->store_id) {
            $storeId = $request->user()->store_id;
            $query->where(function ($q) use ($storeId) {
                $q->where('source_store_id', $storeId)
                  ->orWhere('destination_store_id', $storeId);
            });
        }

        // Search (movement number, supplier, reference, reason)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('movement_number', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%")
                  ->orWhere('external_reference', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        // Date range filters
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        $perPage = (int) $request->input('per_page', 15);
        $movements = $query->latest()->paginate($perPage);

        return $this->paginatedResponse(
            StockMovementResource::collection($movements),
            'Stock movements retrieved successfully.'
        );
    }

    /**
     * Show single movement transaction details.
     */
    public function show(int $id): JsonResponse
    {
        $movement = StockMovement::with(['items.product.brand', 'sourceStore', 'destinationStore', 'user'])->findOrFail($id);

        return $this->successResponse(
            new StockMovementResource($movement),
            'Stock movement details retrieved.'
        );
    }

    /**
     * Stock Receiving: Record received inventory from supplier.
     */
    public function receive(StockReceivingRequest $request): JsonResponse
    {
        try {
            $movement = $this->inventoryService->receiveStock($request->validated(), $request->user());

            return $this->successResponse(
                new StockMovementResource($movement),
                "Stock successfully received and credited to {$movement->destinationStore?->name}. Reference: {$movement->movement_number}",
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Stock Issuing: Issue products out of warehouse with negative-stock prevention.
     */
    public function issue(StockIssuingRequest $request): JsonResponse
    {
        try {
            $movement = $this->inventoryService->issueStock($request->validated(), $request->user());

            return $this->successResponse(
                new StockMovementResource($movement),
                "Stock successfully issued from {$movement->sourceStore?->name}. Reference: {$movement->movement_number}",
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Inter-Store Stock Transfers: Atomic transfer between branches.
     */
    public function transfer(StockTransferRequest $request): JsonResponse
    {
        try {
            $movement = $this->inventoryService->transferStock($request->validated(), $request->user());

            return $this->successResponse(
                new StockMovementResource($movement),
                "Stock successfully transferred from {$movement->sourceStore?->name} to {$movement->destinationStore?->name}. Reference: {$movement->movement_number}",
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Stock Adjustments: Cycle count reconciliation with mandatory reason.
     */
    public function adjust(StockAdjustmentRequest $request): JsonResponse
    {
        try {
            $movement = $this->inventoryService->adjustStock($request->validated(), $request->user());

            return $this->successResponse(
                new StockMovementResource($movement),
                "Stock adjusted successfully for {$movement->sourceStore?->name}. Reference: {$movement->movement_number}",
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }
}
