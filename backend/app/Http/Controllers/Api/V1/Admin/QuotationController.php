<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\QuotationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuotationRequest;
use App\Http\Requests\Admin\UpdateQuotationRequest;
use App\Http\Requests\Admin\UpdateQuotationStatusRequest;
use App\Http\Resources\QuotationResource;
use App\Models\Quotation;
use App\Services\QuotationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QuotationController extends Controller
{
    public function __construct(
        protected QuotationService $quotationService
    ) {}

    /**
     * List all commercial quotations with multi-faceted filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Quotation::with(['store', 'preparedBy', 'salesOrder'])
            ->withCount('items');

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', strtoupper($request->status));
        }

        // Store Filter
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Search Filter (Quotation Number, Customer, Email, Company)
        if ($request->filled('search')) {
            $term = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('quotation_number', 'like', $term)
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

        // Status Counts for Tabs
        $counts = [
            'all' => Quotation::count(),
            'draft' => Quotation::where('status', QuotationStatus::DRAFT)->count(),
            'prepared' => Quotation::where('status', QuotationStatus::PREPARED)->count(),
            'sent' => Quotation::where('status', QuotationStatus::SENT)->count(),
            'approved' => Quotation::where('status', QuotationStatus::APPROVED)->count(),
            'rejected' => Quotation::where('status', QuotationStatus::REJECTED)->count(),
            'converted' => Quotation::where('status', QuotationStatus::CONVERTED)->count(),
            'expired' => Quotation::where('status', QuotationStatus::EXPIRED)->count(),
        ];

        // Summary Metrics (Excluding draft/rejected/converted for pipeline)
        $pipelineVolume = (float) Quotation::whereIn('status', [QuotationStatus::PREPARED, QuotationStatus::SENT, QuotationStatus::APPROVED])->sum('total_amount');
        $approvedVolume = (float) Quotation::where('status', QuotationStatus::APPROVED)->sum('total_amount');

        $quotations = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => QuotationResource::collection($quotations),
            'meta' => [
                'current_page' => $quotations->currentPage(),
                'last_page' => $quotations->lastPage(),
                'per_page' => $quotations->perPage(),
                'total' => $quotations->total(),
                'counts' => $counts,
                'pipeline_volume' => $pipelineVolume,
                'approved_volume' => $approvedVolume,
            ],
        ]);
    }

    /**
     * Display quotation details with line items and audit timeline.
     */
    public function show(Quotation $quotation): JsonResponse
    {
        $quotation->load([
            'items.product.brand',
            'store',
            'preparedBy',
            'statusHistories.user',
            'salesOrder',
            'rfqRequest',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => new QuotationResource($quotation),
        ]);
    }

    /**
     * Create a formal B2B quotation.
     */
    public function store(StoreQuotationRequest $request): JsonResponse
    {
        try {
            $quotation = $this->quotationService->createQuotation(
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'status' => 'success',
                'message' => "Quotation #{$quotation->quotation_number} generated successfully.",
                'data' => new QuotationResource($quotation),
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Update quotation line items and commercial terms.
     */
    public function update(UpdateQuotationRequest $request, Quotation $quotation): JsonResponse
    {
        try {
            $updated = $this->quotationService->updateQuotation(
                $quotation,
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'status' => 'success',
                'message' => "Quotation #{$updated->quotation_number} updated successfully.",
                'data' => new QuotationResource($updated),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Transition quotation through lifecycle status.
     */
    public function updateStatus(UpdateQuotationStatusRequest $request, Quotation $quotation): JsonResponse
    {
        try {
            $updated = $this->quotationService->transitionStatus(
                $quotation,
                QuotationStatus::from($request->status),
                $request->user(),
                $request->notes
            );

            return response()->json([
                'status' => 'success',
                'message' => "Quotation status advanced to {$updated->status->label()}.",
                'data' => new QuotationResource($updated),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Generate and stream/download branded PDF quotation.
     */
    public function downloadPdf(Request $request, Quotation $quotation)
    {
        if ($request->user() && !$request->user()->hasPermission('quotations.pdf') && !$request->user()->hasPermission('quotations.view')) {
            return response()->json(['message' => 'Unauthorized access to quotation PDF document.'], 403);
        }

        $pdf = $this->quotationService->generatePdf($quotation);
        $filename = "iRACK_Quotation_{$quotation->quotation_number}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Delete or archive draft/rejected quotation.
     */
    public function destroy(Request $request, Quotation $quotation): JsonResponse
    {
        if ($quotation->status === QuotationStatus::CONVERTED) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete quotation that has been converted into an active Sales Order.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $quotation->items()->delete();
        $quotation->statusHistories()->delete();
        $quotation->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Quotation #{$quotation->quotation_number} removed successfully.",
        ]);
    }
}
