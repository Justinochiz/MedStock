<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_orderline', function (Blueprint $table) {
            $table->id('service_orderline_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('orderinfo_id');
            $table->integer('quantity')->default(1);

            $table->foreign('service_id')->references('service_id')->on('service')->onDelete('cascade');
            $table->foreign('orderinfo_id')->references('orderinfo_id')->on('orderinfo')->onDelete('cascade');

            $table->index(['orderinfo_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_orderline');
    }
};
