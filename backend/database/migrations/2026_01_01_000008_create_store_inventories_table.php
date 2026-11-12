<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(0)->comment('Current store on-hand stock');
            $table->unsignedInteger('reserved_quantity')->default(0)->comment('Stock allocated to unfulfilled orders');
            $table->unsignedInteger('min_alert_quantity')->default(5)->comment('Threshold for low-stock warnings');
            $table->string('shelf_location', 100)->nullable();
            $table->timestamps();

            $table->unique(['store_id', 'product_id'], 'store_product_unique');
            $table->index(['store_id', 'quantity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_inventories');
    }
};
