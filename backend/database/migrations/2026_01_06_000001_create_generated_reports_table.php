<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('generated_reports')) {
            Schema::create('generated_reports', function (Blueprint $table) {
                $table->id();
                $table->string('report_number', 50)->unique();
                $table->string('type', 50)->index(); // SALES, INVENTORY, SLOW_MOVING, STORE_PERFORMANCE
                $table->string('title', 200);
                $table->string('period_label', 100)->nullable();
                $table->date('from_date')->nullable();
                $table->date('to_date')->nullable();
                $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('set null');
                $table->json('filters')->nullable();
                $table->json('summary_metrics')->nullable();
                $table->string('format', 20)->default('PDF'); // PDF, CSV
                $table->foreignId('generated_by_user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
    }
};
