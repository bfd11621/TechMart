<?php

namespace App\Enums;

enum StockMovementType: string
{
    case RECEIVE = 'RECEIVE';
    case ISSUE = 'ISSUE';
    case TRANSFER_OUT = 'TRANSFER_OUT';
    case TRANSFER_IN = 'TRANSFER_IN';
    case ADJUSTMENT = 'ADJUSTMENT';
    case SALE_DEDUCTION = 'SALE_DEDUCTION';
    case RETURN = 'RETURN';

    public function label(): string
    {
        return match($this) {
            self::RECEIVE => 'Stock Receiving',
            self::ISSUE => 'Stock Issuing',
            self::TRANSFER_OUT => 'Inter-Store Transfer (Out)',
            self::TRANSFER_IN => 'Inter-Store Transfer (In)',
            self::ADJUSTMENT => 'Stock Adjustment',
            self::SALE_DEDUCTION => 'Sales Order Deduction',
            self::RETURN => 'Return Restock',
        };
    }

    public function badgeVariant(): string
    {
        return match($this) {
            self::RECEIVE, self::TRANSFER_IN, self::RETURN => 'success',
            self::ISSUE, self::SALE_DEDUCTION => 'danger',
            self::TRANSFER_OUT => 'info',
            self::ADJUSTMENT => 'warning',
        };
    }

    public function direction(): string
    {
        return match($this) {
            self::RECEIVE, self::TRANSFER_IN, self::RETURN => 'IN',
            self::ISSUE, self::TRANSFER_OUT, self::SALE_DEDUCTION => 'OUT',
            self::ADJUSTMENT => 'ADJUST',
        };
    }
}
