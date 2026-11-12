<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\GeneratedReport;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportService
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    /**
     * Generate unique report reference: REP-{TYPE}-YYYY-XXXXX
     */
    public static function generateReportNumber(string $type = 'SLS'): string
    {
        $year = date('Y');
        do {
            $rand = strtoupper(Str::random(5));
            $number = "REP-{$type}-{$year}-{$rand}";
        } while (GeneratedReport::where('report_number', $number)->exists());

        return $number;
    }

    /**
     * 1. Generate Official Branded Sales PDF Report
     */
    public function generateSalesReportPdf(array $filters, User $user)
    {
        $reportNumber = self::generateReportNumber('SLS');
        $analytics = $this->analyticsService->getSalesAnalytics($filters);

        $storeId = $filters['store_id'] ?? null;
        $store = $storeId ? Store::find($storeId) : null;
        $storeName = $store ? $store->name : 'All Regional Stores (HQ Consolidated)';

        $fromDate = $analytics['period']['from'];
        $toDate = $analytics['period']['to'];

        // Get detailed underlying sales transactions
        $transactions = SalesOrder::with(['store', 'items.product', 'confirmedBy'])
            ->whereIn('status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->whereBetween('confirmed_at', [Carbon::parse($fromDate)->startOfDay(), Carbon::parse($toDate)->endOfDay()])
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->latest('confirmed_at')
            ->get();

        $pdfData = [
            'reportNumber' => $reportNumber,
            'title' => 'Commercial Sales & Revenue Performance Report',
            'periodLabel' => "{$fromDate} to {$toDate}",
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'storeName' => $storeName,
            'generatedAt' => Carbon::now()->format('M d, Y H:i:s'),
            'generatedBy' => $user->name,
            'summary' => $analytics['summary'],
            'byCategory' => $analytics['by_category'],
            'byBrand' => $analytics['by_brand'],
            'byStore' => $analytics['by_store'],
            'transactions' => $transactions,
        ];

        // Save historical report record
        GeneratedReport::create([
            'report_number' => $reportNumber,
            'type' => 'SALES',
            'title' => "Sales Report ({$fromDate} to {$toDate})",
            'period_label' => "{$fromDate} to {$toDate}",
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'store_id' => $storeId,
            'filters' => $filters,
            'summary_metrics' => $analytics['summary'],
            'format' => 'PDF',
            'generated_by_user_id' => $user->id,
        ]);

        $pdf = Pdf::loadView('pdf.sales_report', $pdfData);
        $pdf->setPaper('a4', 'portrait');

        return [
            'pdf' => $pdf,
            'filename' => "iRACK_Sales_Report_{$reportNumber}.pdf",
            'report_number' => $reportNumber,
        ];
    }

    /**
     * 2. Generate Sales Report CSV Export
     */
    public function generateSalesReportCsv(array $filters, User $user): array
    {
        $reportNumber = self::generateReportNumber('CSV');
        $analytics = $this->analyticsService->getSalesAnalytics($filters);

        $storeId = $filters['store_id'] ?? null;
        $fromDate = $analytics['period']['from'];
        $toDate = $analytics['period']['to'];

        $orders = SalesOrder::with(['store', 'items.product.category', 'items.product.brand'])
            ->whereIn('status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->whereBetween('confirmed_at', [Carbon::parse($fromDate)->startOfDay(), Carbon::parse($toDate)->endOfDay()])
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->latest('confirmed_at')
            ->get();

        $csvLines = [];
        // Header
        $csvLines[] = [
            'Sales Order #',
            'Confirmed Date',
            'Fulfillment Store',
            'Customer Name',
            'Company',
            'Product SKU',
            'Product Model',
            'Product Name',
            'Category',
            'Brand',
            'Quantity',
            'Unit Price (SAR)',
            'Line Total (SAR)',
            'Order Status',
            'Payment Status',
        ];

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $csvLines[] = [
                    $order->order_number,
                    $order->confirmed_at ? $order->confirmed_at->format('Y-m-d H:i') : '',
                    $order->store?->name ?? 'N/A',
                    $order->customer_name,
                    $order->company_name ?? '',
                    $item->product?->sku ?? '',
                    $item->product?->model_number ?? '',
                    $item->product?->name ?? '',
                    $item->product?->category?->name ?? '',
                    $item->product?->brand?->name ?? '',
                    $item->quantity,
                    number_format($item->unit_price, 2, '.', ''),
                    number_format($item->total_price, 2, '.', ''),
                    $order->status->value,
                    $order->payment_status->value,
                ];
            }
        }

        // Save historical report record
        GeneratedReport::create([
            'report_number' => $reportNumber,
            'type' => 'SALES',
            'title' => "Sales CSV Export ({$fromDate} to {$toDate})",
            'period_label' => "{$fromDate} to {$toDate}",
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'store_id' => $storeId,
            'filters' => $filters,
            'summary_metrics' => $analytics['summary'],
            'format' => 'CSV',
            'generated_by_user_id' => $user->id,
        ]);

        return [
            'lines' => $csvLines,
            'filename' => "iRACK_Sales_Data_{$reportNumber}.csv",
            'report_number' => $reportNumber,
        ];
    }

    /**
     * 3. Generate Official Branded Inventory PDF Report
     */
    public function generateInventoryReportPdf(array $filters, User $user)
    {
        $reportNumber = self::generateReportNumber('INV');
        $storeId = $filters['store_id'] ?? null;
        $store = $storeId ? Store::find($storeId) : null;
        $storeName = $store ? $store->name : 'All Regional Stores (Consolidated)';

        // Multi-Store Inventory Balances
        $inventories = StoreInventory::with(['product.category', 'product.brand', 'store'])
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->get();

        $totalUnits = $inventories->sum('quantity');
        $totalValuation = $inventories->sum(function ($inv) {
            $unitCost = $inv->product->cost_price ?: $inv->product->base_price;
            return $inv->quantity * $unitCost;
        });

        $lowStockItems = $inventories->filter(fn($inv) => $inv->quantity > 0 && $inv->quantity <= $inv->min_alert_quantity);
        $outOfStockItems = $inventories->filter(fn($inv) => $inv->quantity <= 0);

        // Slow-moving items analysis
        $slowMoving = $this->analyticsService->getSlowMovingProducts([
            'store_id' => $storeId,
            'days_threshold' => $filters['days_threshold'] ?? 60,
        ]);

        $pdfData = [
            'reportNumber' => $reportNumber,
            'title' => 'Multi-Store Inventory & Stock Valuation Audit Report',
            'storeName' => $storeName,
            'generatedAt' => Carbon::now()->format('M d, Y H:i:s'),
            'generatedBy' => $user->name,
            'totalUnits' => $totalUnits,
            'totalValuation' => round($totalValuation, 2),
            'lowStockCount' => $lowStockItems->count(),
            'outOfStockCount' => $outOfStockItems->count(),
            'inventories' => $inventories,
            'lowStockItems' => $lowStockItems,
            'outOfStockItems' => $outOfStockItems,
            'slowMoving' => $slowMoving,
        ];

        // Save report history
        GeneratedReport::create([
            'report_number' => $reportNumber,
            'type' => 'INVENTORY',
            'title' => "Inventory Audit Report ({$storeName})",
            'period_label' => Carbon::now()->format('Y-m-d'),
            'from_date' => Carbon::now()->toDateString(),
            'to_date' => Carbon::now()->toDateString(),
            'store_id' => $storeId,
            'filters' => $filters,
            'summary_metrics' => [
                'total_units' => $totalUnits,
                'total_valuation' => round($totalValuation, 2),
                'low_stock_count' => $lowStockItems->count(),
                'out_of_stock_count' => $outOfStockItems->count(),
            ],
            'format' => 'PDF',
            'generated_by_user_id' => $user->id,
        ]);

        $pdf = Pdf::loadView('pdf.inventory_report', $pdfData);
        $pdf->setPaper('a4', 'landscape'); // Landscape for rich multi-column inventory matrix

        return [
            'pdf' => $pdf,
            'filename' => "iRACK_Inventory_Report_{$reportNumber}.pdf",
            'report_number' => $reportNumber,
        ];
    }

    /**
     * 4. Generate Inventory Report CSV Export
     */
    public function generateInventoryReportCsv(array $filters, User $user): array
    {
        $reportNumber = self::generateReportNumber('CSV');
        $storeId = $filters['store_id'] ?? null;

        $inventories = StoreInventory::with(['product.category', 'product.brand', 'store'])
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->get();

        $csvLines = [];
        $csvLines[] = [
            'Store Name',
            'Store Code',
            'Product SKU',
            'Product Model',
            'Product Name',
            'Category',
            'Brand',
            'Shelf Location',
            'On-Hand Qty',
            'Reserved Qty',
            'Available Qty',
            'Min Alert Threshold',
            'Unit Cost (SAR)',
            'Total Valuation (SAR)',
            'Stock Condition',
            'Last Movement Date',
        ];

        foreach ($inventories as $inv) {
            $unitCost = (float) ($inv->product->cost_price ?: $inv->product->base_price);
            $valuation = (float) $inv->quantity * $unitCost;
            $condition = $inv->quantity <= 0 ? 'OUT_OF_STOCK' : ($inv->quantity <= $inv->min_alert_quantity ? 'LOW_STOCK' : 'IN_STOCK');

            $csvLines[] = [
                $inv->store?->name ?? 'N/A',
                $inv->store?->code ?? '',
                $inv->product?->sku ?? '',
                $inv->product?->model_number ?? '',
                $inv->product?->name ?? '',
                $inv->product?->category?->name ?? '',
                $inv->product?->brand?->name ?? '',
                $inv->shelf_location ?? 'UNASSIGNED',
                $inv->quantity,
                $inv->reserved_quantity,
                $inv->available_quantity,
                $inv->min_alert_quantity,
                number_format($unitCost, 2, '.', ''),
                number_format($valuation, 2, '.', ''),
                $condition,
                $inv->last_movement_at ? $inv->last_movement_at->format('Y-m-d') : 'None',
            ];
        }

        GeneratedReport::create([
            'report_number' => $reportNumber,
            'type' => 'INVENTORY',
            'title' => "Inventory CSV Export",
            'period_label' => Carbon::now()->format('Y-m-d'),
            'from_date' => Carbon::now()->toDateString(),
            'to_date' => Carbon::now()->toDateString(),
            'store_id' => $storeId,
            'filters' => $filters,
            'summary_metrics' => [
                'total_units' => $inventories->sum('quantity'),
            ],
            'format' => 'CSV',
            'generated_by_user_id' => $user->id,
        ]);

        return [
            'lines' => $csvLines,
            'filename' => "iRACK_Inventory_Data_{$reportNumber}.csv",
            'report_number' => $reportNumber,
        ];
    }

    /**
     * 5. Historical Reports Access
     */
    public function getHistoricalReports(array $filters)
    {
        return GeneratedReport::with(['store', 'generatedBy'])
            ->when(!empty($filters['type']), fn($q) => $q->where('type', strtoupper($filters['type'])))
            ->when(!empty($filters['store_id']), fn($q) => $q->where('store_id', $filters['store_id']))
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }
}
