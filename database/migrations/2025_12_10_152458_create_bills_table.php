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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            
            // --- INI KOLOM PENTING YANG KURANG TADI ---
            $table->foreignId('organization_id')->constrained()->onDelete('cascade'); // Relasi ke Organisasi
            $table->string('name'); // Nama Tagihan
            $table->date('due_date'); // Tanggal Jatuh Tempo
            $table->decimal('amount', 15, 0); // Nominal Uang
            // ------------------------------------------

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};