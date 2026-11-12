<?php

namespace App\Enums;

enum QuotationStatus: string
{
    case DRAFT = 'DRAFT';
    case PREPARED = 'PREPARED';
    case SENT = 'SENT';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
    case EXPIRED = 'EXPIRED';
    case CONVERTED = 'CONVERTED';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PREPARED => 'Quotation Prepared',
            self::SENT => 'Sent to Customer',
            self::APPROVED => 'Approved by Customer',
            self::REJECTED => 'Rejected by Customer',
            self::EXPIRED => 'Expired',
            self::CONVERTED => 'Converted to Sales Order',
        };
    }

    public function badgeVariant(): string
    {
        return match($this) {
            self::DRAFT => 'secondary',
            self::PREPARED => 'info',
            self::SENT => 'primary',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::EXPIRED => 'warning',
            self::CONVERTED => 'purple',
        };
    }
}
