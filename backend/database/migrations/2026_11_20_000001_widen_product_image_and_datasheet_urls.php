<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->text('image_url')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->text('datasheet_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('datasheet_url', 255)->nullable()->change();
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->string('image_url', 255)->change();
        });
    }
};
