<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('contact');
            $table->string('name', 120);
            $table->string('email', 150);
            $table->string('phone', 30)->nullable();
            $table->string('company', 150)->nullable();
            $table->string('subject', 200);
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
