<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pembayaran_kas', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
    }

    public function down()
    {
        Schema::table('pembayaran_kas', function (Blueprint $table) {
            $table->unsignedBigInteger('amount')->change();
        });
    }
};

