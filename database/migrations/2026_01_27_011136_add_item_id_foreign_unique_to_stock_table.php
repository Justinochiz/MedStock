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
        Schema::table('stock', function (Blueprint $table) {
            //     $table->foreignId('item_id')
            //         ->unique() // Must be placed before constrained()
            //         ->constrained('item') // Assumes the referenced table is 'users'
            //         ->onDelete('cascade')->change();
            // });
            $table->foreign('item_id')->references('item_id')->on('item')->onDelete('cascade')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
        });
    }
};
