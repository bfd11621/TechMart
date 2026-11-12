<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConvertQuotationRequest;
use App\Http\Requests\Admin\RecordPaymentRequest;
use App\Http\Requests\Admin\StoreDirectSaleRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\SalesOrderResource;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Services\SalesOrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SalesOrderController extends Controller
{
    public function __construct(
        protected SalesOrderService $salesOrderService
    ) {}

    /**
     * List all commercial sales orders with filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = SalesOrder::with(['store', 'quotation', 'createdBy', 'confirmedBy'])
            ->withCount('items');

        // Order Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', strtoupper($request->status));
        }

        // Payment Status Filter
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', strtoupper($request->payment_status));
        }

        // Store Branch Filter
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Search Filter (Order Number, Customer Name, Email, Company)
        if ($request->filled('search')) {
            $term = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', $term)
                  ->orWhere('customer_name', 'like', $term)
                  ->orWhere('customer_email', 'like', $term)
                  ->orWhere('company_name', 'like', $term);
            });
        }

        // Date Range Filtering
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Counts for tabs
        $counts = [
            'all' => SalesOrder::count(),
            'pending' => SalesOrder::where('status', OrderStatus::PENDING)->count(),
            'confirmed' => SalesOrder::where('status', OrderStatus::CONFIRMED)->count(),
            'processing' => SalesOrder::where('status', OrderStatus::PROCESSING)->count(),
            'completed' => SalesOrder::where('status', OrderStatus::COMPLETED)->count(),
            'cancelled' => SalesOrder::where('status', OrderStatus::CANCELLED)->count(),
            'unpaid' => SalesOrder::where('payment_status', PaymentStatus::UNPAID)->count(),
            'partial' => SalesOrder::where('payment_status', PaymentStatus::PARTIAL)->count(),
            'paid' => SalesOrder::where('payment_status', PaymentStatus::PAID)->count(),
        ];

        $orders = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => SalesOrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'counts' => $counts,
            ],
        ]);
    }

    /**
     * Display detailed sales order including BOM items and payment logs.
     */
    public function show(SalesOrder $order): JsonResponse
    {
        $order->load([
            'items.product.brand',
            'store',
            'quotation',
            'createdBy',
            'confirmedBy',
            'payments.receivedBy',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => new SalesOrderResource($order),
        ]);
    }

    /**
     * Convert an approved quotation into a confirmed sales order.
     * Prevents duplicate conversion and deducts physical inventory.
     */
    public function convert(ConvertQuotationRequest $request, Quotation $quotation): JsonResponse
    {
        try {
            $order = $this->salesOrderService->convertQuotationToOrder(
                $quotation,
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'status' => 'success',
                'message' => "Quotation #{$quotation->quotation_number} converted into Sales Order #{$order->order_number}.",
                'data' => new SalesOrderResource($order),
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Create direct sales order (walk-in / emergency corporate purchase).
     */
    public function store(StoreDirectSaleRequest $request): JsonResponse
    {
        try {
            $order = $this->salesOrderService->createDirectSale(
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'status' => 'success',
                'message' => "Direct Sales Order #{$order->order_number} confirmed successfully.",
                'data' => new SalesOrderResource($order),
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Record payment transaction against an order.
     */
    public function recordPayment(RecordPaymentRequest $request, SalesOrder $order): JsonResponse
    {
        try {
            $payment = $this->salesOrderService->recordPayment(
                $order,
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'status' => 'success',
                'message' => "Payment of " . number_format($payment->amount, 2) . " SAR recorded (Receipt #{$payment->receipt_number}).",
                'data' => new PaymentResource($payment),
                'order' => new SalesOrderResource($order->fresh(['payments.receivedBy', 'items.product', 'store'])),
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Sales Dashboard Metrics respecting Revenue Recognition Rules.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $storeId = $request->filled('store_id') ? (int) $request->store_id : null;
        $metrics = $this->salesOrderService->getDashboardMetrics($storeId);

        return response()->json([
            'status' => 'success',
            'data' => $metrics,
        ]);
    }
}
