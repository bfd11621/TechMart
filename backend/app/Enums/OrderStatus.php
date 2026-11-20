<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case PROCESSING = 'PROCESSING';
    case READY_FOR_DISPATCH = 'READY_FOR_DISPATCH';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending Review',
            self::CONFIRMED => 'Confirmed',
            self::PROCESSING => 'Processing',
            self::READY_FOR_DISPATCH => 'Ready for Dispatch',
            self::COMPLETED => 'Delivered',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeVariant(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            self::PROCESSING => 'purple',
            self::READY_FOR_DISPATCH => 'info',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
