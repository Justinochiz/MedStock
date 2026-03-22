<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orderinfo', function (Blueprint $table) {
            $table->date('service_date')->nullable()->after('payment_method');
            $table->time('service_time')->nullable()->after('service_date');
            $table->string('service_mode')->nullable()->after('service_time');
        });
    }

    public function down(): void
    {
        Schema::table('orderinfo', function (Blueprint $table) {
            $table->dropColumn(['service_date', 'service_time', 'service_mode']);
        });
    }
};
