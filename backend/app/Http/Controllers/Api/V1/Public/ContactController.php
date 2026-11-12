<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Handle public contact / consultation inquiry submissions.
     */
    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'company' => 'nullable|string|max:150',
            'department' => 'nullable|string|max:100',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|min:10|max:3000',
            'store_id' => 'nullable|exists:stores,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error on inquiry form.', 422, $validator->errors()->toArray());
        }

        $validated = $validator->validated();

        // Log inquiry for auditing and processing
        Log::info('Public customer inquiry received', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company' => $validated['company'] ?? 'N/A',
            'subject' => $validated['subject'],
        ]);

        return $this->successResponse([
            'reference_ticket' => 'INQ-' . strtoupper(bin2hex(random_bytes(4))),
            'received_at' => now()->toIso8601String(),
        ], 'Your inquiry has been received. An enterprise solutions engineer will respond within 24 business hours.');
    }
}
