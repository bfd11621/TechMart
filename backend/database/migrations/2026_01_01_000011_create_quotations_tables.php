<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number', 50)->unique()->index();
            $table->foreignId('rfq_request_id')->nullable()->constrained('rfq_requests')->onDelete('set null');
            $table->foreignId('store_id')->constrained('stores')->onDelete('restrict');
            $table->string('customer_name', 150)->index();
            $table->string('customer_email', 150)->index();
            $table->string('customer_phone', 50);
            $table->string('company_name', 150)->nullable();
            $table->text('billing_address')->nullable();
            $table->string('status', 50)->default('DRAFT')->index();
            $table->date('valid_until')->index();
            $table->decimal('subtotal', 14, 2)->default(0.00);
            $table->string('discount_type', 20)->default('fixed'); // fixed, percentage
            $table->decimal('discount_value', 14, 2)->default(0.00);
            $table->decimal('discount_amount', 14, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(0.00); // e.g. 15.00%
            $table->decimal('tax_amount', 14, 2)->default(0.00);
            $table->decimal('total_amount', 14, 2)->default(0.00);
            $table->text('terms_conditions')->nullable();
            $table->foreignId('prepared_by_user_id')->constrained('users')->onDelete('restrict');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 14, 2)->default(0.00);
            $table->decimal('discount_percent', 5, 2)->default(0.00);
            $table->decimal('total_price', 14, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
