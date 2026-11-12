<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique()->index();
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->onDelete('set null');
            $table->foreignId('store_id')->constrained('stores')->onDelete('restrict');
            $table->string('customer_name', 150)->index();
            $table->string('customer_email', 150)->index();
            $table->string('customer_phone', 50);
            $table->string('company_name', 150)->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('status', 50)->default('PENDING')->index();
            $table->string('payment_status', 50)->default('UNPAID')->index();
            $table->string('payment_method', 50)->nullable();
            $table->decimal('subtotal', 14, 2)->default(0.00);
            $table->decimal('discount_amount', 14, 2)->default(0.00);
            $table->decimal('tax_amount', 14, 2)->default(0.00);
            $table->decimal('total_amount', 14, 2)->default(0.00)->index();
            $table->decimal('paid_amount', 14, 2)->default(0.00);
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('confirmed_by_user_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamp('confirmed_at')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_cost', 14, 2)->default(0.00);
            $table->decimal('unit_price', 14, 2)->default(0.00);
            $table->decimal('discount_amount', 14, 2)->default(0.00);
            $table->decimal('total_price', 14, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->onDelete('cascade');
            $table->string('receipt_number', 50)->unique()->index();
            $table->decimal('amount', 14, 2);
            $table->string('payment_method', 50); // Cash, Bank Transfer, Card, Cheque
            $table->string('payment_reference', 100)->nullable();
            $table->date('payment_date')->index();
            $table->text('notes')->nullable();
            $table->foreignId('received_by_user_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
    }
};
