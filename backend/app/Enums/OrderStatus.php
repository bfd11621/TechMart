<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case PROCESSING = 'PROCESSING';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending Review',
            self::CONFIRMED => 'Confirmed (Revenue Active)',
            self::PROCESSING => 'Fulfilling & Staging',
            self::COMPLETED => 'Delivered / Completed',
            self::CANCELLED => 'Cancelled (Zero Revenue)',
        };
    }

    public function badgeVariant(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            self::PROCESSING => 'purple',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
