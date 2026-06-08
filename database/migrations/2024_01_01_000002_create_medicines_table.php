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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->text('description')->nullable();
            $table->text('composition')->nullable();         // Komposisi / kandungan obat
            $table->text('indications')->nullable();         // Indikasi / kegunaan
            $table->text('dosage')->nullable();              // Dosis & aturan pakai
            $table->text('side_effects')->nullable();        // Efek samping
            $table->text('contraindications')->nullable();   // Kontra indikasi
            $table->string('unit', 50)->default('pcs');      // Satuan: pcs, strip, botol, dll
            $table->decimal('price', 12, 2);
            $table->decimal('price_before_discount', 12, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(10);       // Minimum stok sebelum notifikasi
            $table->string('image')->nullable();
            $table->boolean('requires_prescription')->default(false); // Butuh resep dokter
            $table->boolean('is_active')->default(true);
            $table->date('expired_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
