<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Enums\RfqStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicRfqRequest;
use App\Http\Resources\RfqRequestResource;
use App\Mail\AdminRfqNotificationMail;
use App\Mail\CustomerRfqConfirmationMail;
use App\Models\RfqItem;
use App\Models\RfqRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class RfqSubmissionController extends Controller
{
    /**
     * Submit a public customer Request for Quotation (RFQ).
     */
    public function store(StorePublicRfqRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $rfq = DB::transaction(function () use ($validated) {
                // 1. Generate guaranteed unique reference number: RFQ-YYYY-XXXXX
                $year = date('Y');
                do {
                    $randomSuffix = strtoupper(Str::random(5));
                    $rfqNumber = "RFQ-{$year}-{$randomSuffix}";
                } while (RfqRequest::where('rfq_number', $rfqNumber)->exists());

                // 2. Create RFQ Master Record
                $rfqRecord = RfqRequest::create([
                    'rfq_number' => $rfqNumber,
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'customer_phone' => $validated['customer_phone'],
                    'company_name' => $validated['company_name'] ?? null,
                    'location' => $validated['location'] ?? ($validated['city'] ?? $validated['address'] ?? null),
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'preferred_store_id' => $validated['preferred_store_id'] ?? null,
                    'status' => RfqStatus::NEW_REQUEST,
                    'customer_notes' => $validated['customer_notes'] ?? null,
                    'submitted_at' => now(),
                ]);

                // 3. Persist Requested Items
                foreach ($validated['items'] as $item) {
                    RfqItem::create([
                        'rfq_request_id' => $rfqRecord->id,
                        'product_id' => $item['product_id'],
                        'quantity' => (int) $item['quantity'],
                        'target_price' => $item['target_price'] ?? null,
                        'notes' => $item['notes'] ?? null,
                    ]);
                }

                // 4. Record Initial Status History
                $rfqRecord->statusHistories()->create([
                    'status' => RfqStatus::NEW_REQUEST,
                    'previous_status' => null,
                    'user_id' => null,
                    'notes' => 'Quotation request submitted through online product showroom.',
                ]);

                return $rfqRecord;
            });

            // 5. Load relationships for response and email rendering
            $rfq->load(['items.product', 'preferredStore', 'statusHistories']);

            // 6. Asynchronous / Non-blocking Email Notifications
            try {
                // Fetch designated recipient inboxes from dynamic system settings
                $recipients = null;
                try {
                    $recipients = \App\Models\SystemSetting::get('email.rfq_recipients');
                } catch (Throwable) {
                    // Fallback if settings table unmigrated
                }

                if (empty($recipients) || !is_array($recipients)) {
                    $recipients = ['dagimw@iracktech.com', config('mail.admin_address', 'admin@irack.com')];
                }

                // Deliver branded HTML alert to staff administrators
                foreach ($recipients as $recipient) {
                    if (!empty($recipient)) {
                        Mail::to($recipient)->send(new AdminRfqNotificationMail($rfq));
                        Log::info("Dispatched Admin RFQ notification email for {$rfq->rfq_number} to {$recipient}");
                    }
                }

                // Deliver customer confirmation copy
                if (!empty($rfq->customer_email)) {
                    Mail::to($rfq->customer_email)->send(new CustomerRfqConfirmationMail($rfq));
                    Log::info("Dispatched Customer RFQ confirmation email for {$rfq->rfq_number} to {$rfq->customer_email}");
                }
            } catch (Throwable $mailEx) {
                // Log email delivery issue without breaking customer transaction
                Log::warning('RFQ notification email delivery notice: ' . $mailEx->getMessage(), [
                    'rfq_number' => $rfq->rfq_number,
                ]);
            }

            return $this->successResponse(
                new RfqRequestResource($rfq),
                "Your Request for Quotation has been received with reference {$rfq->rfq_number}. A dedicated solutions architect will contact you within 24 business hours.",
                201
            );
        } catch (Throwable $e) {
            Log::error('Failed to process RFQ submission: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return $this->errorResponse(
                'An error occurred while registering your quotation request. Please try again or contact our technical sales desk directly.',
                500
            );
        }
    }
}
