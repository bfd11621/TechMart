<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthCheckController extends Controller
{
    /**
     * System health check endpoint.
     */
    public function index(): JsonResponse
    {
        $dbConnected = false;
        $dbError = null;

        try {
            DB::connection()->getPdo();
            $dbConnected = true;
        } catch (Throwable $e) {
            $dbError = $e->getMessage();
        }

        $payload = [
            'system' => 'iRACK IT Solution — Showroom & Inventory Management',
            'version' => '1.0.0',
            'environment' => config('app.env'),
            'timestamp' => now()->toIso8601String(),
            'database' => [
                'status' => $dbConnected ? 'connected' : 'disconnected',
                'error' => $dbError,
            ],
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];

        return $this->successResponse(
            $payload,
            $dbConnected ? 'System is fully operational.' : 'System initialized (Database offline).'
        );
    }
}
