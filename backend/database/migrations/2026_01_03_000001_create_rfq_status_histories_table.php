<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfq_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfq_request_id')->constrained('rfq_requests')->onDelete('cascade');
            $table->string('status', 50)->index();
            $table->string('previous_status', 50)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Staff user responsible, or null if customer submission');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_status_histories');
    }
};
