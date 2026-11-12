<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case UNPAID = 'UNPAID';
    case PARTIAL = 'PARTIAL';
    case PAID = 'PAID';
    case REFUNDED = 'REFUNDED';

    public function label(): string
    {
        return match($this) {
            self::UNPAID => 'Unpaid',
            self::PARTIAL => 'Partially Paid',
            self::PAID => 'Fully Paid',
            self::REFUNDED => 'Refunded',
        };
    }

    public function badgeVariant(): string
    {
        return match($this) {
            self::UNPAID => 'danger',
            self::PARTIAL => 'warning',
            self::PAID => 'success',
            self::REFUNDED => 'secondary',
        };
    }
}
