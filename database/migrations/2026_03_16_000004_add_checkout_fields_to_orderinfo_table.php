<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orderinfo', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('customer_id');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->text('shipping_address')->nullable()->after('customer_phone');
            $table->string('discount_code')->nullable()->after('shipping');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_code');
            $table->decimal('subtotal_amount', 10, 2)->default(0)->after('discount_amount');
            $table->decimal('total_amount', 10, 2)->default(0)->after('subtotal_amount');
            $table->string('payment_method')->nullable()->after('total_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orderinfo', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name',
                'customer_phone',
                'shipping_address',
                'discount_code',
                'discount_amount',
                'subtotal_amount',
                'total_amount',
                'payment_method',
            ]);
        });
    }
};
