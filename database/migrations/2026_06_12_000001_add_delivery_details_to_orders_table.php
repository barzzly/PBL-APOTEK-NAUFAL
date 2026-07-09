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
        Schema::table('orders', function (Blueprint $table) {
            $table->double('delivery_latitude')->nullable()->after('payment_proof');
            $table->double('delivery_longitude')->nullable()->after('delivery_latitude');
            $table->double('delivery_distance')->nullable()->after('delivery_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_latitude', 'delivery_longitude', 'delivery_distance']);
        });
    }
};
