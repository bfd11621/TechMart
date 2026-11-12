<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SyncPermissionsRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    /**
     * List all permissions grouped by module.
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::orderBy('module')->orderBy('id')->get();

        $grouped = $permissions->groupBy('module')->map(function ($items, $module) {
            return [
                'module' => $module,
                'label' => ucwords(str_replace('_', ' ', $module)),
                'permissions' => PermissionResource::collection($items),
            ];
        })->values();

        return $this->successResponse([
            'all' => PermissionResource::collection($permissions),
            'grouped' => $grouped,
        ], 'Permissions retrieved successfully.');
    }

    /**
     * Synchronize permissions assigned to a specific role.
     */
    public function syncRolePermissions(SyncPermissionsRequest $request, Role $role): JsonResponse
    {
        if ($role->slug === RoleEnum::SUPER_ADMIN->value) {
            return $this->errorResponse('Super Administrator always retains all system permissions.', 422);
        }

        $oldPermissions = $role->permissions()->pluck('id')->toArray();
        $role->permissions()->sync($request->permission_ids);

        AuditService::log('PERMISSIONS_SYNCED', $role, ['permission_ids' => $oldPermissions], ['permission_ids' => $request->permission_ids]);

        return $this->successResponse(
            new RoleResource($role->load(['permissions'])->loadCount('users')),
            "Permissions for role '{$role->name}' have been synchronized successfully."
        );
    }
}
