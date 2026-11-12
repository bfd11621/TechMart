<?php

namespace App\Http\Resources;

use App\Enums\QuotationStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationStatusHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $statusEnum = $this->status instanceof QuotationStatus
            ? $this->status
            : QuotationStatus::tryFrom($this->status);

        return [
            'id' => $this->id,
            'status' => $this->status,
            'status_label' => $statusEnum?->label() ?? $this->status,
            'badge_variant' => $statusEnum?->badgeVariant() ?? 'neutral',
            'previous_status' => $this->previous_status,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'formatted_created_at' => $this->created_at?->format('M d, Y H:i'),
        ];
    }
}
