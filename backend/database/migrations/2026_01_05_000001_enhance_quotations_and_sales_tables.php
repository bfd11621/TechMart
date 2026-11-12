<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add notes to quotations table if missing
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'notes')) {
                $table->text('notes')->nullable()->after('terms_conditions');
            }
            if (!Schema::hasColumn('quotations', 'client_notes')) {
                $table->text('client_notes')->nullable()->after('notes');
            }
        });

        // 2. Create quotation_status_histories
        if (!Schema::hasTable('quotation_status_histories')) {
            Schema::create('quotation_status_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
                $table->string('status', 50)->index();
                $table->string('previous_status', 50)->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_status_histories');

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['notes', 'client_notes']);
        });
    }
};
