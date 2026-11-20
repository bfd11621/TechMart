<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->dropSecondaryIndexes('product_specifications', ['spec_group', 'spec_name']);

        Schema::table('product_specifications', function (Blueprint $table) {
            $table->text('spec_group')->nullable()->change();
            $table->text('spec_name')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->mediumText('datasheet_url')->nullable()->change();
            $table->text('datasheet_filename')->nullable()->change();
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->mediumText('image_url')->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->mediumText('image_url')->nullable()->change();
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->mediumText('logo_url')->nullable()->change();
            $table->text('website')->nullable()->change();
        });

        if (Schema::hasTable('banners')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->mediumText('image_url')->nullable()->change();
            });
        }

        if (Schema::hasColumn('sales_orders', 'client_po_file')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->text('client_po_file')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (Schema::hasColumn('sales_orders', 'client_po_file')) {
                $table->string('client_po_file', 255)->nullable()->change();
            }
        });

        if (Schema::hasTable('banners')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->string('image_url', 500)->nullable()->change();
            });
        }

        Schema::table('brands', function (Blueprint $table) {
            $table->string('website', 255)->nullable()->change();
            $table->string('logo_url', 255)->nullable()->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('image_url', 255)->nullable()->change();
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->text('image_url')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('datasheet_filename', 255)->nullable()->change();
            $table->text('datasheet_url')->nullable()->change();
        });

        Schema::table('product_specifications', function (Blueprint $table) {
            $table->string('spec_name', 150)->change();
            $table->string('spec_group', 100)->default('General')->change();
        });
    }

    private function dropSecondaryIndexes(string $table, array $columns): void
    {
        $indexes = DB::select('SHOW INDEX FROM `' . $table . '`');
        $names = [];

        foreach ($indexes as $index) {
            if ($index->Key_name === 'PRIMARY') {
                continue;
            }
            if (in_array($index->Column_name, $columns, true)) {
                $names[$index->Key_name] = true;
            }
        }

        foreach (array_keys($names) as $name) {
            DB::statement('ALTER TABLE `' . $table . '` DROP INDEX `' . $name . '`');
        }
    }
};
