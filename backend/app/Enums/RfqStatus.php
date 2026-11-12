<?php

namespace App\Enums;

enum RfqStatus: string
{
    case NEW_REQUEST = 'NEW_REQUEST';
    case UNDER_REVIEW = 'UNDER_REVIEW';
    case QUOTATION_PREPARED = 'QUOTATION_PREPARED';
    case SENT_TO_CUSTOMER = 'SENT_TO_CUSTOMER';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
    case CONVERTED_TO_ORDER = 'CONVERTED_TO_ORDER';
    case COMPLETED = 'COMPLETED';

    public function label(): string
    {
        return match($this) {
            self::NEW_REQUEST => 'New Request',
            self::UNDER_REVIEW => 'Under Review',
            self::QUOTATION_PREPARED => 'Quotation Prepared',
            self::SENT_TO_CUSTOMER => 'Sent to Customer',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::CONVERTED_TO_ORDER => 'Converted to Sales Order',
            self::COMPLETED => 'Completed',
        };
    }

    public function badgeVariant(): string
    {
        return match($this) {
            self::NEW_REQUEST => 'primary',
            self::UNDER_REVIEW => 'warning',
            self::QUOTATION_PREPARED => 'corporate',
            self::SENT_TO_CUSTOMER => 'info',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::CONVERTED_TO_ORDER => 'purple',
            self::COMPLETED => 'gray',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
