<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembayaran_kas_activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->cascadeOnDelete();

            // transaksi yang disentuh (boleh null kalau sewaktu-waktu record transaksi dihapus)
            $table->foreignId('pembayaran_kas_id')
                ->nullable()
                ->constrained('pembayaran_kas')
                ->nullOnDelete();

            // siapa yang melakukan aksi (admin/bendahara)
            $table->foreignId('actor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // role actor saat kejadian (disimpan supaya histori tetap valid walau role berubah)
            $table->string('actor_role', 20)->default('member');

            // siapa yang "dibayarkan" (user_id di pembayaran_kas)
            $table->foreignId('payer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // tipe aksi: create / status_change / update / delete / verify / reject, dll
            $table->string('action', 40);

            // snapshot perubahan
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // jejak request (instrumen pengawasan)
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'created_at']);
            $table->index(['actor_id', 'created_at']);
            $table->index(['payer_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_kas_activity_logs');
    }
};
