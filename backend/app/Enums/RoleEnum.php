<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'super_admin';
    case STORE_MANAGER = 'store_manager';
    case SALES_MANAGER = 'sales_manager';
    case INVENTORY_OFFICER = 'inventory_officer';
    case GENERAL_ADMIN = 'general_admin';

    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::STORE_MANAGER => 'Store Manager',
            self::SALES_MANAGER => 'Sales Manager',
            self::INVENTORY_OFFICER => 'Inventory Officer',
            self::GENERAL_ADMIN => 'General Administrator',
        };
    }
}
