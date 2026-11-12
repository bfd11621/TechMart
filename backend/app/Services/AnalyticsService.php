<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\QuotationStatus;
use App\Enums\RfqStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\RfqRequest;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Store;
use App\Models\StoreInventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * 1. Executive Management Dashboard High-Level Metrics
     */
    public function getManagementDashboardMetrics(?int $storeId = null): array
    {
        $today = Carbon::today()->toDateString();
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();

        // 1. Catalog & Stores
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $activeStores = Store::where('is_active', true)->count();

        // 2. Inventory Metrics
        $inventoryQuery = StoreInventory::query()
            ->when($storeId, fn($q) => $q->where('store_id', $storeId));

        $currentStockQuantity = (int) (clone $inventoryQuery)->sum('quantity');

        // Total inventory value: sum(quantity * coalesce(cost_price, base_price))
        $totalInventoryValue = (float) DB::table('store_inventories')
            ->join('products', 'store_inventories.product_id', '=', 'products.id')
            ->when($storeId, fn($q) => $q->where('store_inventories.store_id', $storeId))
            ->sum(DB::raw('store_inventories.quantity * COALESCE(products.cost_price, products.base_price, 0)'));

        $lowStockProducts = (int) (clone $inventoryQuery)
            ->where('quantity', '>', 0)
            ->whereRaw('quantity <= min_alert_quantity')
            ->count();

        $outOfStockProducts = (int) (clone $inventoryQuery)
            ->where('quantity', '<=', 0)
            ->count();

        // 3. Sales & Revenue Metrics (STRICT REVENUE RECOGNITION RULE: Confirmed, Processing, Completed only)
        $salesOrderQuery = SalesOrder::query()
            ->whereIn('status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->when($storeId, fn($q) => $q->where('store_id', $storeId));

        $totalConfirmedRevenue = (float) (clone $salesOrderQuery)->sum('total_amount');
        $dailySales = (float) (clone $salesOrderQuery)->whereDate('confirmed_at', $today)->sum('total_amount');
        $weeklySales = (float) (clone $salesOrderQuery)->whereDate('confirmed_at', '>=', $startOfWeek)->sum('total_amount');
        $monthlySales = (float) (clone $salesOrderQuery)->whereDate('confirmed_at', '>=', $startOfMonth)->sum('total_amount');

        // Completed sales orders
        $completedSalesOrders = SalesOrder::query()
            ->where('status', OrderStatus::COMPLETED->value)
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->count();

        $activeOrdersCount = (clone $salesOrderQuery)->count();

        // 4. Quotations & RFQs
        $totalRfqs = RfqRequest::count();
        $pendingQuotations = Quotation::whereIn('status', [QuotationStatus::PREPARED->value, QuotationStatus::SENT->value])
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->count();

        // 5. Recent Confirmed Transactions Feed
        $recentSales = SalesOrder::with(['store', 'items.product'])
            ->whereIn('status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->latest('confirmed_at')
            ->take(5)
            ->get()
            ->map(fn($o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'customer_name' => $o->customer_name,
                'company_name' => $o->company_name,
                'store_name' => $o->store?->name,
                'total_amount' => (float) $o->total_amount,
                'status' => $o->status->value,
                'status_label' => $o->status->label(),
                'payment_status' => $o->payment_status->value,
                'payment_status_label' => $o->payment_status->label(),
                'confirmed_at' => $o->confirmed_at?->format('M d, Y H:i'),
                'items_count' => $o->items->count(),
            ]);

        return [
            'total_products' => $totalProducts,
            'total_categories' => $totalCategories,
            'active_stores' => $activeStores,
            'current_stock_quantity' => $currentStockQuantity,
            'total_inventory_value' => round($totalInventoryValue, 2),
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts,
            'total_confirmed_revenue' => round($totalConfirmedRevenue, 2),
            'daily_sales' => round($dailySales, 2),
            'weekly_sales' => round($weeklySales, 2),
            'monthly_sales' => round($monthlySales, 2),
            'total_rfqs' => $totalRfqs,
            'pending_quotations' => $pendingQuotations,
            'completed_sales_orders' => $completedSalesOrders,
            'active_orders_count' => $activeOrdersCount,
            'recent_sales' => $recentSales,
        ];
    }

    /**
     * 2. Detailed Sales Analytics with Dynamic Time Horizon, Multi-Store, and Categorical Groupings
     */
    public function getSalesAnalytics(array $filters): array
    {
        $dates = $this->resolveDateRange($filters);
        $fromDate = $dates['from'];
        $toDate = $dates['to'];
        $storeId = $filters['store_id'] ?? null;
        $storeIds = $filters['store_ids'] ?? null;

        // Base recognized revenue orders query
        $ordersQuery = SalesOrder::query()
            ->whereIn('status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->whereBetween('confirmed_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->when(!empty($storeIds) && is_array($storeIds), fn($q) => $q->whereIn('store_id', $storeIds));

        $totalRevenue = (float) (clone $ordersQuery)->sum('total_amount');
        $totalOrders = (clone $ordersQuery)->count();
        $totalCollected = (float) (clone $ordersQuery)->sum('paid_amount');
        $outstandingReceivables = max(0, $totalRevenue - $totalCollected);

        // Calculate Previous Period for Growth Comparison
        $diffDays = $fromDate->diffInDays($toDate) + 1;
        $prevFromDate = $fromDate->copy()->subDays($diffDays);
        $prevToDate = $fromDate->copy()->subDay();

        $prevRevenue = (float) SalesOrder::query()
            ->whereIn('status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->whereBetween('confirmed_at', [$prevFromDate->startOfDay(), $prevToDate->endOfDay()])
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->when(!empty($storeIds) && is_array($storeIds), fn($q) => $q->whereIn('store_id', $storeIds))
            ->sum('total_amount');

        $revenueGrowthPercent = $prevRevenue > 0
            ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : ($totalRevenue > 0 ? 100.0 : 0.0);

        // Time Series Sales Trend (Daily or Monthly depending on range)
        $trendFormat = $diffDays <= 31 ? '%Y-%m-%d' : '%Y-%m';
        $salesTrend = DB::table('sales_orders')
            ->select(
                DB::raw("DATE_FORMAT(confirmed_at, '{$trendFormat}') as period"),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(id) as orders_count')
            )
            ->whereIn('status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->whereBetween('confirmed_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->when(!empty($storeIds) && is_array($storeIds), fn($q) => $q->whereIn('store_id', $storeIds))
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get()
            ->map(fn($row) => [
                'period' => $row->period,
                'revenue' => (float) $row->revenue,
                'orders_count' => (int) $row->orders_count,
            ]);

        // Sales by Category
        $salesByCategory = DB::table('sales_order_items')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->join('products', 'sales_order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(sales_order_items.total_price) as revenue'),
                DB::raw('SUM(sales_order_items.quantity) as units_sold')
            )
            ->whereIn('sales_orders.status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->whereBetween('sales_orders.confirmed_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
            ->when($storeId, fn($q) => $q->where('sales_orders.store_id', $storeId))
            ->when(!empty($storeIds) && is_array($storeIds), fn($q) => $q->whereIn('sales_orders.store_id', $storeIds))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('revenue', 'desc')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'revenue' => (float) $c->revenue,
                'units_sold' => (int) $c->units_sold,
                'percentage' => $totalRevenue > 0 ? round(((float)$c->revenue / $totalRevenue) * 100, 1) : 0,
            ]);

        // Sales by Brand
        $salesByBrand = DB::table('sales_order_items')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->join('products', 'sales_order_items.product_id', '=', 'products.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->select(
                'brands.id',
                'brands.name',
                DB::raw('SUM(sales_order_items.total_price) as revenue'),
                DB::raw('SUM(sales_order_items.quantity) as units_sold')
            )
            ->whereIn('sales_orders.status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->whereBetween('sales_orders.confirmed_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
            ->when($storeId, fn($q) => $q->where('sales_orders.store_id', $storeId))
            ->when(!empty($storeIds) && is_array($storeIds), fn($q) => $q->whereIn('sales_orders.store_id', $storeIds))
            ->groupBy('brands.id', 'brands.name')
            ->orderBy('revenue', 'desc')
            ->get()
            ->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'revenue' => (float) $b->revenue,
                'units_sold' => (int) $b->units_sold,
                'percentage' => $totalRevenue > 0 ? round(((float)$b->revenue / $totalRevenue) * 100, 1) : 0,
            ]);

        // Sales by Store
        $salesByStore = DB::table('sales_orders')
            ->join('stores', 'sales_orders.store_id', '=', 'stores.id')
            ->select(
                'stores.id',
                'stores.name',
                'stores.code',
                DB::raw('SUM(sales_orders.total_amount) as revenue'),
                DB::raw('COUNT(sales_orders.id) as orders_count')
            )
            ->whereIn('sales_orders.status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->whereBetween('sales_orders.confirmed_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
            ->groupBy('stores.id', 'stores.name', 'stores.code')
            ->orderBy('revenue', 'desc')
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'code' => $s->code,
                'revenue' => (float) $s->revenue,
                'orders_count' => (int) $s->orders_count,
                'percentage' => $totalRevenue > 0 ? round(((float)$s->revenue / $totalRevenue) * 100, 1) : 0,
            ]);

        return [
            'period' => [
                'from' => $fromDate->toDateString(),
                'to' => $toDate->toDateString(),
                'diff_days' => $diffDays,
                'preset' => $filters['period'] ?? 'custom',
            ],
            'summary' => [
                'total_revenue' => round($totalRevenue, 2),
                'total_orders' => $totalOrders,
                'total_collected' => round($totalCollected, 2),
                'outstanding_receivables' => round($outstandingReceivables, 2),
                'previous_period_revenue' => round($prevRevenue, 2),
                'growth_percent' => $revenueGrowthPercent,
            ],
            'trend' => $salesTrend,
            'by_category' => $salesByCategory,
            'by_brand' => $salesByBrand,
            'by_store' => $salesByStore,
        ];
    }

    /**
     * 3. Best-Selling Products by Volume & Revenue
     */
    public function getBestSellingProducts(array $filters): array
    {
        $dates = $this->resolveDateRange($filters);
        $fromDate = $dates['from'];
        $toDate = $dates['to'];
        $storeId = $filters['store_id'] ?? null;
        $limit = (int) ($filters['limit'] ?? 10);

        // Top Products by Quantity Sold
        $topByQuantity = DB::table('sales_order_items')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->join('products', 'sales_order_items.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.name',
                'products.model_number',
                'products.sku',
                'brands.name as brand_name',
                'categories.name as category_name',
                DB::raw('SUM(sales_order_items.quantity) as total_quantity_sold'),
                DB::raw('SUM(sales_order_items.total_price) as total_revenue_generated'),
                DB::raw('AVG(sales_order_items.unit_price) as average_unit_price')
            )
            ->whereIn('sales_orders.status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->whereBetween('sales_orders.confirmed_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
            ->when($storeId, fn($q) => $q->where('sales_orders.store_id', $storeId))
            ->groupBy('products.id', 'products.name', 'products.model_number', 'products.sku', 'brands.name', 'categories.name')
            ->orderBy('total_quantity_sold', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'model_number' => $p->model_number,
                'sku' => $p->sku,
                'brand_name' => $p->brand_name ?? 'iRACK Partner',
                'category_name' => $p->category_name ?? 'IT Solutions',
                'total_quantity_sold' => (int) $p->total_quantity_sold,
                'total_revenue_generated' => (float) $p->total_revenue_generated,
                'average_unit_price' => round((float) $p->average_unit_price, 2),
            ]);

        // Top Products by Revenue
        $topByRevenue = DB::table('sales_order_items')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->join('products', 'sales_order_items.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.name',
                'products.model_number',
                'products.sku',
                'brands.name as brand_name',
                'categories.name as category_name',
                DB::raw('SUM(sales_order_items.quantity) as total_quantity_sold'),
                DB::raw('SUM(sales_order_items.total_price) as total_revenue_generated'),
                DB::raw('AVG(sales_order_items.unit_price) as average_unit_price')
            )
            ->whereIn('sales_orders.status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->whereBetween('sales_orders.confirmed_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
            ->when($storeId, fn($q) => $q->where('sales_orders.store_id', $storeId))
            ->groupBy('products.id', 'products.name', 'products.model_number', 'products.sku', 'brands.name', 'categories.name')
            ->orderBy('total_revenue_generated', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'model_number' => $p->model_number,
                'sku' => $p->sku,
                'brand_name' => $p->brand_name ?? 'iRACK Partner',
                'category_name' => $p->category_name ?? 'IT Solutions',
                'total_quantity_sold' => (int) $p->total_quantity_sold,
                'total_revenue_generated' => (float) $p->total_revenue_generated,
                'average_unit_price' => round((float) $p->average_unit_price, 2),
            ]);

        return [
            'top_by_quantity' => $topByQuantity,
            'top_by_revenue' => $topByRevenue,
        ];
    }

    /**
     * 4. Slow-Moving and Non-Moving Products Analysis
     * Clearly distinguishes between:
     * - Stock Age: days since initial receipt/creation in warehouse
     * - Time Since Last Sale: days since product appeared in a confirmed order
     */
    public function getSlowMovingProducts(array $filters): array
    {
        $daysThreshold = (int) ($filters['days_threshold'] ?? 60);
        $storeId = $filters['store_id'] ?? null;
        $now = Carbon::now();
        $cutoffDate = $now->copy()->subDays($daysThreshold);

        // Subquery for the most recent confirmed sale date for each product
        $lastSaleSubquery = DB::table('sales_order_items')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->whereIn('sales_orders.status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->select('sales_order_items.product_id', DB::raw('MAX(sales_orders.confirmed_at) as last_sold_at'))
            ->groupBy('sales_order_items.product_id');

        // Query active inventory across products
        $slowProducts = DB::table('store_inventories')
            ->join('products', 'store_inventories.product_id', '=', 'products.id')
            ->join('stores', 'store_inventories.store_id', '=', 'stores.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoinSub($lastSaleSubquery, 'last_sales', function ($join) {
                $join->on('products.id', '=', 'last_sales.product_id');
            })
            ->where('store_inventories.quantity', '>', 0)
            ->when($storeId, fn($q) => $q->where('store_inventories.store_id', $storeId))
            ->where(function ($q) use ($cutoffDate) {
                // Non-moving: never sold, or last sold before threshold
                $q->whereNull('last_sales.last_sold_at')
                  ->orWhere('last_sales.last_sold_at', '<', $cutoffDate);
            })
            ->select(
                'products.id',
                'products.name',
                'products.model_number',
                'products.sku',
                'categories.name as category_name',
                'brands.name as brand_name',
                'stores.name as store_name',
                'stores.code as store_code',
                'store_inventories.quantity as on_hand_quantity',
                'store_inventories.created_at as stocked_since',
                'store_inventories.last_movement_at',
                'last_sales.last_sold_at',
                DB::raw('COALESCE(products.cost_price, products.base_price, 0) as unit_cost')
            )
            ->orderBy('store_inventories.quantity', 'desc')
            ->get()
            ->map(function ($row) use ($now) {
                $stockedAt = $row->stocked_since ? Carbon::parse($row->stocked_since) : $now;
                $stockAgeDays = (int) $stockedAt->diffInDays($now);

                $lastSoldAt = $row->last_sold_at ? Carbon::parse($row->last_sold_at) : null;
                $daysSinceLastSale = $lastSoldAt ? (int) $lastSoldAt->diffInDays($now) : null;

                $tiedUpCapital = (float) $row->on_hand_quantity * (float) $row->unit_cost;

                $riskLevel = 'MODERATE';
                if ($daysSinceLastSale === null && $stockAgeDays > 90) {
                    $riskLevel = 'CRITICAL_DEAD_STOCK';
                } elseif ($daysSinceLastSale !== null && $daysSinceLastSale > 120) {
                    $riskLevel = 'HIGH_STAGNANT';
                }

                return [
                    'product_id' => $row->id,
                    'name' => $row->name,
                    'model_number' => $row->model_number,
                    'sku' => $row->sku,
                    'category_name' => $row->category_name ?? 'General',
                    'brand_name' => $row->brand_name ?? 'OEM',
                    'store_name' => $row->store_name,
                    'on_hand_quantity' => (int) $row->on_hand_quantity,
                    'unit_cost' => (float) $row->unit_cost,
                    'tied_up_capital' => round($tiedUpCapital, 2),
                    'stock_age_days' => $stockAgeDays,
                    'last_sold_at' => $lastSoldAt?->format('Y-m-d'),
                    'days_since_last_sale' => $daysSinceLastSale,
                    'risk_level' => $riskLevel,
                ];
            });

        $totalTiedCapital = $slowProducts->sum('tied_up_capital');

        return [
            'threshold_days' => $daysThreshold,
            'total_slow_moving_items' => $slowProducts->count(),
            'total_tied_capital' => round($totalTiedCapital, 2),
            'items' => $slowProducts,
        ];
    }

    /**
     * 5. Multi-Store Side-by-Side Performance Comparison
     */
    public function getStoreComparisonAnalytics(?array $storeIds = null): array
    {
        $stores = Store::where('is_active', true)
            ->when(!empty($storeIds), fn($q) => $q->whereIn('id', $storeIds))
            ->get();

        $comparison = [];

        foreach ($stores as $store) {
            // Confirmed sales revenue
            $confirmedRevenue = (float) SalesOrder::where('store_id', $store->id)
                ->whereIn('status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
                ->sum('total_amount');

            $confirmedOrdersCount = SalesOrder::where('store_id', $store->id)
                ->whereIn('status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
                ->count();

            // Total physical units sold
            $unitsSold = (int) DB::table('sales_order_items')
                ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
                ->where('sales_orders.store_id', $store->id)
                ->whereIn('sales_orders.status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
                ->sum('sales_order_items.quantity');

            // On-hand inventory volume and valuation
            $stockQuantity = (int) StoreInventory::where('store_id', $store->id)->sum('quantity');
            $stockValuation = (float) DB::table('store_inventories')
                ->join('products', 'store_inventories.product_id', '=', 'products.id')
                ->where('store_inventories.store_id', $store->id)
                ->sum(DB::raw('store_inventories.quantity * COALESCE(products.cost_price, products.base_price, 0)'));

            // Top product for this store
            $topProduct = DB::table('sales_order_items')
                ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
                ->join('products', 'sales_order_items.product_id', '=', 'products.id')
                ->where('sales_orders.store_id', $store->id)
                ->whereIn('sales_orders.status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
                ->select('products.name', 'products.model_number', DB::raw('SUM(sales_order_items.total_price) as rev'))
                ->groupBy('products.name', 'products.model_number')
                ->orderBy('rev', 'desc')
                ->first();

            $comparison[] = [
                'store_id' => $store->id,
                'store_name' => $store->name,
                'store_code' => $store->code,
                'is_primary' => (bool) $store->is_primary,
                'confirmed_revenue' => round($confirmedRevenue, 2),
                'confirmed_orders_count' => $confirmedOrdersCount,
                'units_sold' => $unitsSold,
                'stock_quantity' => $stockQuantity,
                'stock_valuation' => round($stockValuation, 2),
                'top_product_name' => $topProduct ? $topProduct->name : 'N/A',
                'top_product_model' => $topProduct ? $topProduct->model_number : 'N/A',
                'top_product_revenue' => $topProduct ? (float) $topProduct->rev : 0.0,
            ];
        }

        return $comparison;
    }

    /**
     * Helper to resolve Date Range from presets or custom inputs
     */
    private function resolveDateRange(array $filters): array
    {
        $preset = $filters['period'] ?? 'this_month';
        $now = Carbon::now();

        switch ($preset) {
            case 'today':
                return ['from' => $now->copy()->startOfDay(), 'to' => $now->copy()->endOfDay()];
            case 'this_week':
                return ['from' => $now->copy()->startOfWeek(), 'to' => $now->copy()->endOfWeek()];
            case 'this_month':
                return ['from' => $now->copy()->startOfMonth(), 'to' => $now->copy()->endOfMonth()];
            case 'this_quarter':
                return ['from' => $now->copy()->firstOfQuarter(), 'to' => $now->copy()->lastOfQuarter()];
            case 'this_year':
                return ['from' => $now->copy()->startOfYear(), 'to' => $now->copy()->endOfYear()];
            case 'custom':
            default:
                if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
                    return [
                        'from' => Carbon::parse($filters['from_date'])->startOfDay(),
                        'to' => Carbon::parse($filters['to_date'])->endOfDay(),
                    ];
                }
                // Fallback default: past 30 days
                return ['from' => $now->copy()->subDays(30)->startOfDay(), 'to' => $now->copy()->endOfDay()];
        }
    }
}
