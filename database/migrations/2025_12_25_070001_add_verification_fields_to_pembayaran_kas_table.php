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
        Schema::table('pembayaran_kas', function (Blueprint $table) {
            // waktu diverifikasi admin
            if (! Schema::hasColumn('pembayaran_kas', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('payment_date');
            }

            // admin/bendahara yang memverifikasi
            if (! Schema::hasColumn('pembayaran_kas', 'verified_by')) {
                $table->foreignId('verified_by')
                    ->nullable()
                    ->after('verified_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // alasan penolakan
            if (! Schema::hasColumn('pembayaran_kas', 'rejection_reason')) {
                $table->text('rejection_reason')
                    ->nullable()
                    ->after('verified_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_kas', function (Blueprint $table) {

            if (Schema::hasColumn('pembayaran_kas', 'verified_by')) {
                $table->dropForeign(['verified_by']);
                $table->dropColumn('verified_by');
            }

            if (Schema::hasColumn('pembayaran_kas', 'verified_at')) {
                $table->dropColumn('verified_at');
            }

            if (Schema::hasColumn('pembayaran_kas', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
