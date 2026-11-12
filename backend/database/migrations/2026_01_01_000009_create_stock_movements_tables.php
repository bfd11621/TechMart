<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('movement_number', 50)->unique()->index();
            $table->string('type', 50)->index()->comment('RECEIVE, ISSUE, TRANSFER_OUT, TRANSFER_IN, ADJUSTMENT, SALE_DEDUCTION, RETURN');
            $table->foreignId('source_store_id')->nullable()->constrained('stores')->onDelete('restrict');
            $table->foreignId('destination_store_id')->nullable()->constrained('stores')->onDelete('restrict');
            $table->string('reference_type', 100)->nullable()->comment('Polymorphic relation or doc name: SalesOrder, Quotation, PurchaseOrder');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict')->comment('Staff member who performed/authorized');
            $table->text('reason')->nullable()->comment('Mandatory for adjustments');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('stock_movement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_movement_id')->constrained('stock_movements')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->integer('quantity')->comment('Positive for addition, negative for deduction depending on context');
            $table->decimal('unit_cost', 14, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movement_items');
        Schema::dropIfExists('stock_movements');
    }
};
