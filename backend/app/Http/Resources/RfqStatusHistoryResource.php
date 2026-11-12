<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RfqStatusHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'badge_variant' => $this->status->badgeVariant(),
            'previous_status' => $this->previous_status?->value,
            'previous_status_label' => $this->previous_status?->label(),
            'user_name' => $this->user?->name ?? 'System / Public Customer',
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'formatted_date' => $this->created_at?->format('M d, Y H:i'),
        ];
    }
}
