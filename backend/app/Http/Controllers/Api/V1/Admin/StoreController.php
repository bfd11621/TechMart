<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStoreRequest;
use App\Http\Requests\Admin\UpdateStoreRequest;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * List all physical branch locations.
     */
    public function index(): JsonResponse
    {
        $stores = Store::with(['manager', 'users.role'])
            ->withCount('users')
            ->orderBy('is_primary', 'desc')
            ->orderBy('name')
            ->get();

        return $this->successResponse(
            StoreResource::collection($stores),
            'Stores retrieved successfully.'
        );
    }

    /**
     * Create a new store branch.
     */
    public function store(StoreStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (!empty($data['is_primary'])) {
            Store::where('is_primary', true)->update(['is_primary' => false]);
        }

        $store = Store::create($data);

        // If manager assigned, ensure user's store_id is set to this store
        if (!empty($data['manager_id'])) {
            User::where('id', $data['manager_id'])->update(['store_id' => $store->id]);
        }

        AuditService::log('STORE_CREATED', $store, null, $store->toArray());

        return $this->successResponse(
            new StoreResource($store->load(['manager', 'users'])),
            'Store branch established successfully.',
            201
        );
    }

    /**
     * Show single store.
     */
    public function show(Store $store): JsonResponse
    {
        return $this->successResponse(
            new StoreResource($store->load(['manager', 'users.role'])->loadCount('users')),
            'Store details retrieved.'
        );
    }

    /**
     * Update store branch.
     */
    public function update(UpdateStoreRequest $request, Store $store): JsonResponse
    {
        $data = $request->validated();

        if (!empty($data['is_primary']) && !$store->is_primary) {
            Store::where('is_primary', true)->update(['is_primary' => false]);
        }

        $oldValues = $store->toArray();
        $store->update($data);

        if (isset($data['manager_id']) && $data['manager_id']) {
            User::where('id', $data['manager_id'])->update(['store_id' => $store->id]);
        }

        AuditService::log('STORE_UPDATED', $store, $oldValues, $store->toArray());

        return $this->successResponse(
            new StoreResource($store->load(['manager', 'users.role'])),
            'Store details updated successfully.'
        );
    }

    /**
     * Activate or deactivate store branch.
     */
    public function toggleStatus(Store $store): JsonResponse
    {
        if ($store->is_primary && $store->is_active) {
            return $this->errorResponse('The primary Central Distribution Hub (HQ) cannot be deactivated.', 422);
        }

        $oldStatus = $store->is_active;
        $store->update(['is_active' => !$oldStatus]);

        AuditService::log('STORE_STATUS_TOGGLED', $store, ['is_active' => $oldStatus], ['is_active' => $store->is_active]);

        $statusLabel = $store->is_active ? 'activated' : 'deactivated';
        return $this->successResponse(
            new StoreResource($store->load(['manager', 'users.role'])),
            "Store '{$store->name}' has been {$statusLabel}."
        );
    }

    /**
     * Assign employees to a store branch.
     */
    public function assignEmployees(Request $request, Store $store): JsonResponse
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        // Unassign existing users who are no longer in the list (except if user is super admin or if this is HQ)
        User::where('store_id', $store->id)
            ->whereNotIn('id', $validated['user_ids'])
            ->update(['store_id' => null]);

        // Assign users to this store
        User::whereIn('id', $validated['user_ids'])->update(['store_id' => $store->id]);

        AuditService::log('STORE_EMPLOYEES_ASSIGNED', $store, null, [
            'store_id' => $store->id,
            'assigned_users_count' => count($validated['user_ids']),
        ]);

        return $this->successResponse(
            new StoreResource($store->fresh(['manager', 'users.role'])),
            'Store employees synchronized successfully.'
        );
    }

    /**
     * Remove a store branch.
     */
    public function destroy(Store $store): JsonResponse
    {
        if ($store->is_primary) {
            return $this->errorResponse('The primary Central Distribution Hub (HQ) cannot be removed.', 422);
        }

        if ($store->inventories()->where('quantity', '>', 0)->exists()) {
            return $this->errorResponse('Cannot delete a store with positive on-hand inventory. Please transfer all stock first.', 422);
        }

        if ($store->users()->count() > 0) {
            return $this->errorResponse('Cannot delete a store with assigned staff members. Please reassign staff first.', 422);
        }

        $storeName = $store->name;
        $store->delete();

        AuditService::log('STORE_DELETED', null, ['name' => $storeName]);

        return $this->successResponse(null, "Store branch '{$storeName}' has been removed.");
    }
}
