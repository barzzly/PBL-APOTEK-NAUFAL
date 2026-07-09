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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->string('prescription_number')->unique();  // Nomor resep
            $table->string('doctor_name');                    // Nama dokter penulis resep
            $table->string('hospital_clinic')->nullable();    // Rumah sakit / klinik
            $table->date('prescription_date');                // Tanggal resep
            $table->string('patient_name');                   // Nama pasien (bisa beda dengan user)
            $table->integer('patient_age')->nullable();       // Umur pasien
            $table->enum('status', [
                'pending',          // Menunggu verifikasi apoteker
                'verified',         // Sudah diverifikasi apoteker
                'processing',       // Sedang disiapkan
                'completed',        // Obat sudah diserahkan
                'rejected',         // Resep ditolak
            ])->default('pending');
            $table->string('image');                          // Foto / scan resep
            $table->text('notes')->nullable();                // Catatan apoteker
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null'); // Apoteker yang verifikasi
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
