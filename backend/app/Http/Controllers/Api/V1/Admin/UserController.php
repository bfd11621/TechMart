<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetUserPasswordRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * List users with search, filtering, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['role', 'store']);

        // Search
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Role Filter
        if ($roleId = $request->query('role_id')) {
            $query->where('role_id', $roleId);
        }

        // Store Filter
        if ($storeId = $request->query('store_id')) {
            $query->where('store_id', $storeId);
        }

        // Status Filter
        if ($request->has('is_active') && $request->query('is_active') !== '') {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = max(5, min(100, (int) $request->query('per_page', 15)));
        $users = $query->orderBy('id', 'desc')->paginate($perPage);

        return $this->paginatedResponse($users, 'Staff directory retrieved successfully.');
    }

    /**
     * Create a new administrative user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->load(['role', 'store']);

        AuditService::log('USER_CREATED', $user, null, $user->only(['name', 'email', 'role_id', 'store_id']));

        return $this->successResponse(
            new UserResource($user),
            'Staff member created successfully.',
            201
        );
    }

    /**
     * Show a single user details.
     */
    public function show(User $user): JsonResponse
    {
        return $this->successResponse(
            new UserResource($user->load(['role.permissions', 'store'])),
            'User details retrieved.'
        );
    }

    /**
     * Update user details and assignments.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $oldValues = $user->only(['name', 'email', 'phone', 'role_id', 'store_id', 'is_active']);

        $user->update($request->validated());
        $user->load(['role', 'store']);

        AuditService::log('USER_UPDATED', $user, $oldValues, $user->only(['name', 'email', 'phone', 'role_id', 'store_id', 'is_active']));

        return $this->successResponse(
            new UserResource($user),
            'Staff member details updated successfully.'
        );
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(User $user): JsonResponse
    {
        if ($user->id === Auth::id()) {
            return $this->errorResponse('You cannot deactivate your own administrative account.', 422);
        }

        $oldStatus = $user->is_active;
        $user->update(['is_active' => ! $oldStatus]);

        if (! $user->is_active) {
            // Revoke active sessions immediately
            $user->tokens()->delete();
        }

        AuditService::log(
            $user->is_active ? 'USER_ACTIVATED' : 'USER_DEACTIVATED',
            $user,
            ['is_active' => $oldStatus],
            ['is_active' => $user->is_active]
        );

        return $this->successResponse(
            new UserResource($user->load(['role', 'store'])),
            "Staff account has been " . ($user->is_active ? 'activated.' : 'deactivated.')
        );
    }

    /**
     * Reset a user's password directly by an authorized administrator.
     */
    public function resetPassword(ResetUserPasswordRequest $request, User $user): JsonResponse
    {
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Revoke active tokens so user must log in with new credentials
        $user->tokens()->delete();

        AuditService::log('ADMIN_RESET_PASSWORD', $user);

        return $this->successResponse(null, "Password for {$user->name} has been reset successfully.");
    }

    /**
     * Delete an administrative user.
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user->id === Auth::id()) {
            return $this->errorResponse('You cannot delete your own account.', 422);
        }

        if ($user->isSuperAdmin() && User::where('role_id', $user->role_id)->count() <= 1) {
            return $this->errorResponse('Cannot delete the primary Super Administrator account.', 422);
        }

        $user->tokens()->delete();
        $userName = $user->name;
        $user->delete();

        AuditService::log('USER_DELETED', null, ['id' => $user->id, 'name' => $userName]);

        return $this->successResponse(null, "User '{$userName}' has been deleted.");
    }
}
