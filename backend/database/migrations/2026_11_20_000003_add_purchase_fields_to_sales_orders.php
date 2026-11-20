<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_orders', 'client_po_number')) {
                $table->string('client_po_number', 100)->nullable()->after('quotation_id');
            }
            if (!Schema::hasColumn('sales_orders', 'client_po_file')) {
                $table->string('client_po_file', 255)->nullable()->after('client_po_number');
            }
            if (!Schema::hasColumn('sales_orders', 'payment_reference')) {
                $table->string('payment_reference', 100)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('sales_orders', 'expected_delivery_date')) {
                $table->date('expected_delivery_date')->nullable()->after('shipping_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            foreach (['client_po_number', 'client_po_file', 'payment_reference', 'expected_delivery_date'] as $column) {
                if (Schema::hasColumn('sales_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
