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
        Schema::table('prescriptions', function (Blueprint $table) {
            // Drop prescription_date column
            if (Schema::hasColumn('prescriptions', 'prescription_date')) {
                $table->dropColumn('prescription_date');
            }

            // Modify doctor_name and image to be nullable
            $table->string('doctor_name')->nullable()->change();
            $table->string('image')->nullable()->change();

            // Add type column
            $table->enum('type', ['prescription', 'consultation'])->default('prescription')->after('prescription_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->date('prescription_date')->nullable();
            $table->string('doctor_name')->nullable(false)->change();
            $table->string('image')->nullable(false)->change();
            $table->dropColumn('type');
        });
    }
};
