<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('key_features')->nullable()->after('full_description')->comment('List of bulleted key selling features');
            $table->string('availability_status', 50)->default('IN_STOCK')->after('is_published')->index()->comment('IN_STOCK, LOW_STOCK, OUT_OF_STOCK, PRE_ORDER, DISCONTINUED');
            $table->string('datasheet_filename', 255)->nullable()->after('datasheet_url');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['key_features', 'availability_status', 'datasheet_filename']);
        });
    }
};
