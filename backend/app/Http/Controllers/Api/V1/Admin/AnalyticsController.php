<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    /**
     * 1. Executive Management Dashboard High-Level Metrics
     */
    public function dashboard(Request $request): JsonResponse
    {
        $storeId = $request->filled('store_id') ? (int) $request->store_id : null;
        $metrics = $this->analyticsService->getManagementDashboardMetrics($storeId);

        return response()->json([
            'status' => 'success',
            'data' => $metrics,
        ]);
    }

    /**
     * 2. Comprehensive Sales Analytics with ECharts Time Series & Breakdowns
     */
    public function sales(Request $request): JsonResponse
    {
        $filters = [
            'period' => $request->input('period', 'this_month'),
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
            'store_id' => $request->filled('store_id') ? (int) $request->store_id : null,
            'store_ids' => $request->filled('store_ids') ? (array) $request->store_ids : null,
        ];

        $analytics = $this->analyticsService->getSalesAnalytics($filters);

        return response()->json([
            'status' => 'success',
            'data' => $analytics,
        ]);
    }

    /**
     * 3. Best-Selling Products by Volume & Revenue
     */
    public function bestSellers(Request $request): JsonResponse
    {
        $filters = [
            'period' => $request->input('period', 'this_month'),
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
            'store_id' => $request->filled('store_id') ? (int) $request->store_id : null,
            'limit' => $request->input('limit', 10),
        ];

        $data = $this->analyticsService->getBestSellingProducts($filters);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * 4. Slow-Moving and Non-Moving Products Analysis
     */
    public function slowMoving(Request $request): JsonResponse
    {
        $filters = [
            'store_id' => $request->filled('store_id') ? (int) $request->store_id : null,
            'days_threshold' => $request->input('days_threshold', 60),
        ];

        $data = $this->analyticsService->getSlowMovingProducts($filters);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * 5. Multi-Store Side-by-Side Performance Comparison
     */
    public function storeComparison(Request $request): JsonResponse
    {
        $storeIds = $request->filled('store_ids') ? (array) $request->store_ids : null;
        $data = $this->analyticsService->getStoreComparisonAnalytics($storeIds);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
