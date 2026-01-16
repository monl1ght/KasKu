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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Kode unik untuk join (misal: KASKU-AB12)
            $table->string('account_number')->nullable(); // No Rekening (opsional sekarang)
            $table->timestamps();
        });

        // Tabel Pivot (Penghubung antara User dan Organisasi)
        Schema::create('organization_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member'); // 'admin' (bendahara) atau 'member'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop pivot dulu (karen a ada foreign key ke organizations)
        Schema::dropIfExists('organization_user');
        Schema::dropIfExists('organizations');
    }
};
