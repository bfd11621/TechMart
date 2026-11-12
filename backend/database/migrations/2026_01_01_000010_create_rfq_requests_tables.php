<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfq_requests', function (Blueprint $table) {
            $table->id();
            $table->string('rfq_number', 50)->unique()->index();
            $table->string('customer_name', 150)->index();
            $table->string('customer_email', 150)->index();
            $table->string('customer_phone', 50);
            $table->string('company_name', 150)->nullable();
            $table->string('tax_number', 50)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('location', 255)->nullable()->comment('Delivery site or client location');
            $table->foreignId('preferred_store_id')->nullable()->constrained('stores')->onDelete('set null');
            $table->string('status', 50)->default('NEW_REQUEST')->index();
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('rfq_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfq_request_id')->constrained('rfq_requests')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('target_price', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_items');
        Schema::dropIfExists('rfq_requests');
    }
};
