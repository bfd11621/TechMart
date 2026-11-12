<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Homepage Banners Table
        if (!Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
                $table->id();
                $table->string('title', 200);
                $table->string('subtitle', 300)->nullable();
                $table->string('badge_text', 100)->nullable();
                $table->string('image_url', 500);
                $table->string('cta_button_text', 100)->nullable();
                $table->string('cta_button_link', 300)->nullable();
                $table->string('secondary_button_text', 100)->nullable();
                $table->string('secondary_button_link', 300)->nullable();
                $table->integer('sort_order')->default(0)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        // 2. Centralized System Settings Table
        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('group', 50)->index(); // company, email, system, merchandising, navigation
                $table->string('key', 100)->unique();
                $table->longText('value')->nullable();
                $table->string('type', 30)->default('string'); // string, json, boolean, integer, encrypted
                $table->boolean('is_public')->default(false)->index();
                $table->string('description', 255)->nullable();
                $table->timestamps();
            });
        }

        // 3. Website Pages / Contents Table
        if (!Schema::hasTable('website_contents')) {
            Schema::create('website_contents', function (Blueprint $table) {
                $table->id();
                $table->string('slug', 100)->unique();
                $table->string('title', 200);
                $table->string('subtitle', 300)->nullable();
                $table->longText('content')->nullable();
                $table->string('meta_title', 200)->nullable();
                $table->string('meta_description', 300)->nullable();
                $table->json('sections_data')->nullable();
                $table->boolean('is_published')->default(true)->index();
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('website_contents');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('banners');
    }
};
