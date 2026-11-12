<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add manager_id to stores table if not already present
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'manager_id')) {
                $table->foreignId('manager_id')->nullable()->after('is_primary')->constrained('users')->nullOnDelete();
            }
        });

        // 2. Add last_movement_at to store_inventories table
        Schema::table('store_inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('store_inventories', 'last_movement_at')) {
                $table->timestamp('last_movement_at')->nullable()->after('shelf_location');
            }
        });

        // 3. Add supplier, external_reference, status to stock_movements table
        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_movements', 'supplier')) {
                $table->string('supplier', 150)->nullable()->after('type');
            }
            if (!Schema::hasColumn('stock_movements', 'external_reference')) {
                $table->string('external_reference', 100)->nullable()->after('supplier')->comment('PO number, RMA ref, or physical count sheet');
            }
            if (!Schema::hasColumn('stock_movements', 'status')) {
                $table->string('status', 50)->default('COMPLETED')->after('external_reference')->comment('COMPLETED, PENDING, CANCELLED');
            }
        });

        // 4. Add previous_quantity and new_quantity to stock_movement_items table
        Schema::table('stock_movement_items', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_movement_items', 'previous_quantity')) {
                $table->integer('previous_quantity')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('stock_movement_items', 'new_quantity')) {
                $table->integer('new_quantity')->nullable()->after('previous_quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_movement_items', function (Blueprint $table) {
            $table->dropColumn(['previous_quantity', 'new_quantity']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn(['supplier', 'external_reference', 'status']);
        });

        Schema::table('store_inventories', function (Blueprint $table) {
            $table->dropColumn('last_movement_at');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn('manager_id');
        });
    }
};
