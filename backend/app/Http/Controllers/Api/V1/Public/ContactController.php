<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Handle public contact / consultation inquiry submissions.
     */
    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:contact,registration',
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
        $type = $validated['type'] ?? 'contact';

        $inquiry = Inquiry::create([
            'type' => $type,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        $reference = 'INQ-'.str_pad((string) $inquiry->id, 6, '0', STR_PAD_LEFT);

        try {
            $body = implode("\n", [
                'Reference: '.$reference,
                'Type: '.$type,
                'Name: '.$inquiry->name,
                'Email: '.$inquiry->email,
                'Phone: '.($inquiry->phone ?: '—'),
                'Company: '.($inquiry->company ?: '—'),
                'Subject: '.$inquiry->subject,
                '',
                $inquiry->message,
            ]);
            Mail::raw($body, function ($message) use ($inquiry, $reference, $type) {
                $label = $type === 'registration' ? 'Customer registration' : 'Contact inquiry';
                $message->to('dagicreative16@gmail.com')
                    ->replyTo($inquiry->email, $inquiry->name)
                    ->subject($label.' '.$reference.' — '.$inquiry->name);
            });
        } catch (\Throwable $e) {
            Log::warning('Inquiry mail failed for '.$reference.': '.$e->getMessage());
        }

        $message = $type === 'registration'
            ? 'Your company registration has been received. Our sales desk will contact you shortly.'
            : 'Your inquiry has been received. An enterprise solutions engineer will respond within 24 business hours.';

        return $this->successResponse([
            'reference_ticket' => $reference,
            'received_at' => $inquiry->created_at?->toIso8601String(),
        ], $message);
    }
}
