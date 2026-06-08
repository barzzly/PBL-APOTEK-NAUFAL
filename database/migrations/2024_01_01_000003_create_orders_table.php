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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();         // Nomor order unik (e.g. ORD-2024-001)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', [
                'pending',          // Menunggu konfirmasi
                'confirmed',        // Dikonfirmasi apotek
                'processing',       // Sedang diproses/disiapkan
                'ready_for_pickup', // Siap diambil
                'shipped',          // Sedang dikirim
                'delivered',        // Sudah diterima
                'cancelled',        // Dibatalkan
            ])->default('pending');
            $table->enum('order_type', ['pickup', 'delivery'])->default('pickup');  // Ambil di apotek / dikirim
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('payment_method', ['cash', 'transfer', 'bpjs', 'qris'])->default('cash');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_proof')->nullable();      // Bukti transfer
            $table->text('shipping_address')->nullable();
            $table->string('notes')->nullable();              // Catatan dari customer
            $table->string('pharmacist_note')->nullable();    // Catatan dari apoteker
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
