<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * List all roles with member counts and permissions.
     */
    public function index(): JsonResponse
    {
        $roles = Role::with(['permissions'])->withCount('users')->get();

        return $this->successResponse(
            RoleResource::collection($roles),
            'Roles retrieved successfully.'
        );
    }

    /**
     * Create a new custom role.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = Role::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'is_system' => false,
        ]);

        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->permission_ids);
        }

        $role->load(['permissions'])->loadCount('users');

        AuditService::log('ROLE_CREATED', $role, null, $role->toArray());

        return $this->successResponse(
            new RoleResource($role),
            'Custom role created successfully.',
            201
        );
    }

    /**
     * Show single role.
     */
    public function show(Role $role): JsonResponse
    {
        return $this->successResponse(
            new RoleResource($role->load(['permissions'])->loadCount('users')),
            'Role details retrieved.'
        );
    }

    /**
     * Update role details.
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $oldValues = $role->only(['name', 'description']);

        $role->update($request->validated());

        AuditService::log('ROLE_UPDATED', $role, $oldValues, $role->only(['name', 'description']));

        return $this->successResponse(
            new RoleResource($role->load(['permissions'])->loadCount('users')),
            'Role updated successfully.'
        );
    }

    /**
     * Delete custom role.
     */
    public function destroy(Role $role): JsonResponse
    {
        if ($role->is_system) {
            return $this->errorResponse('System-defined enterprise roles cannot be deleted.', 422);
        }

        if ($role->users()->count() > 0) {
            return $this->errorResponse('Cannot delete a role that still has assigned staff members. Please reassign them first.', 422);
        }

        $roleName = $role->name;
        $role->permissions()->detach();
        $role->delete();

        AuditService::log('ROLE_DELETED', null, ['name' => $roleName]);

        return $this->successResponse(null, "Role '{$roleName}' has been removed.");
    }
}
