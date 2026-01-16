<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Pastikan data lama sudah di-backfill
        DB::statement("
            ALTER TABLE pembayaran_kas
            MODIFY type ENUM('pemasukan','pengeluaran')
            NOT NULL
            DEFAULT 'pemasukan'
        ");
    }

    public function down()
    {
        Schema::table('pembayaran_kas', function (Blueprint $table) {
            $table->string('type')->nullable()->change();
        });
    }
};