<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->foreignId('brand_id')->constrained('brands')->onDelete('restrict');
            $table->string('sku', 100)->unique()->index();
            $table->string('name', 255)->index();
            $table->string('slug', 255)->unique();
            $table->string('model_number', 100)->nullable()->index();
            $table->text('short_description')->nullable();
            $table->longText('full_description')->nullable();
            $table->decimal('base_price', 14, 2)->default(0.00)->index();
            $table->decimal('cost_price', 14, 2)->default(0.00);
            $table->string('datasheet_url', 255)->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_published')->default(true)->index();
            $table->string('warranty_info', 150)->nullable();
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('image_url', 255);
            $table->boolean('is_primary')->default(false)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('product_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('spec_group', 100)->default('General')->index();
            $table->string('spec_name', 150)->index();
            $table->text('spec_value');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_specifications');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }
};
