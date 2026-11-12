<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\RfqStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateRfqStatusRequest;
use App\Http\Resources\RfqRequestResource;
use App\Models\RfqRequest;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RfqManagementController extends Controller
{
    public function __construct(protected AuditService $audit)
    {
    }

    /**
     * Display a paginated listing of RFQs with faceted filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = RfqRequest::query()
            ->with(['items.product', 'preferredStore', 'assignedUser', 'statusHistories'])
            ->orderBy('id', 'desc');

        // Search by RFQ number, customer name, company, email, or phone
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('rfq_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Store / Branch Filter
        if ($storeId = $request->query('store_id')) {
            $query->where('preferred_store_id', $storeId);
        }

        $perPage = max(5, min(50, (int) $request->query('per_page', 15)));
        $rfqs = $query->paginate($perPage);

        return $this->paginatedResponse(
            $rfqs->through(fn ($rfq) => new RfqRequestResource($rfq)),
            'RFQs retrieved successfully.'
        );
    }

    /**
     * Display the specified RFQ details with complete status timeline.
     */
    public function show(RfqRequest $rfq): JsonResponse
    {
        $rfq->load([
            'items.product.primaryImage',
            'items.product.brand',
            'preferredStore',
            'assignedUser',
            'statusHistories.user',
            'quotation',
        ]);

        return $this->successResponse(
            new RfqRequestResource($rfq),
            'RFQ details retrieved.'
        );
    }

    /**
     * Update RFQ lifecycle status with responsible staff logging.
     */
    public function updateStatus(UpdateRfqStatusRequest $request, RfqRequest $rfq): JsonResponse
    {
        $oldStatus = $rfq->status->value;
        $newStatus = $request->status;
        $notes = $request->notes;

        // Perform status transition and record timeline history
        $history = $rfq->logStatusChange(Auth::user(), $newStatus, $notes);

        // Audit Trail
        $this->audit->log(
            'RFQ_STATUS_UPDATED',
            $rfq,
            ['status' => $oldStatus],
            ['status' => $newStatus, 'notes' => $notes]
        );

        $rfq->load(['items.product', 'preferredStore', 'assignedUser', 'statusHistories.user']);

        return $this->successResponse(
            new RfqRequestResource($rfq),
            "RFQ {$rfq->rfq_number} status updated to " . RfqStatus::from($newStatus)->label() . "."
        );
    }

    /**
     * Update internal staff notes and engineer assignment.
     */
    public function updateNotes(Request $request, RfqRequest $rfq): JsonResponse
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:3000',
            'assigned_to_user_id' => 'nullable|exists:users,id',
        ]);

        $rfq->update($validated);

        $this->audit->log('RFQ_NOTES_UPDATED', $rfq, null, $validated);

        return $this->successResponse(
            new RfqRequestResource($rfq->fresh(['assignedUser', 'statusHistories'])),
            'RFQ internal notes updated.'
        );
    }

    /**
     * Delete or archive an unneeded RFQ.
     */
    public function destroy(RfqRequest $rfq): JsonResponse
    {
        if (in_array($rfq->status, [RfqStatus::CONVERTED_TO_ORDER, RfqStatus::COMPLETED])) {
            return $this->errorResponse('Cannot delete an RFQ that has already been converted to a sales order or completed.', 422);
        }

        $rfqNumber = $rfq->rfq_number;
        $rfq->delete();

        $this->audit->log('RFQ_DELETED', null, ['rfq_number' => $rfqNumber]);

        return $this->successResponse(null, "RFQ {$rfqNumber} has been removed.");
    }
}
