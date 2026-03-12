<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('item', function (Blueprint $table) {
            $table->text('gallery_paths')->nullable()->after('img_path');
        });

        Schema::table('service', function (Blueprint $table) {
            $table->text('gallery_paths')->nullable()->after('img_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item', function (Blueprint $table) {
            $table->dropColumn('gallery_paths');
        });

        Schema::table('service', function (Blueprint $table) {
            $table->dropColumn('gallery_paths');
        });
    }
};
