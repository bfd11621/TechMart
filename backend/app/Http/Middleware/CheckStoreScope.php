<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStoreScope
{
    /**
     * Ensure the user only accesses resources for their assigned store unless Super Admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated access.',
            ], 401);
        }

        // Global access roles
        if ($user->isSuperAdmin() || is_null($user->store_id)) {
            return $next($request);
        }

        // If request includes a store_id parameter, verify match
        $requestedStoreId = $request->route('store')?->id 
            ?? $request->route('storeId') 
            ?? $request->input('store_id');

        if ($requestedStoreId && (int)$requestedStoreId !== (int)$user->store_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized branch access. You may only manage records within your assigned store.',
            ], 403);
        }

        return $next($request);
    }
}
