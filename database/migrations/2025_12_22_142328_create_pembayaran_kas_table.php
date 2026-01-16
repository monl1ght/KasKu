<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaranKasTable extends Migration
{
    public function up()
    {
        Schema::create('pembayaran_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bill_id')->nullable()->constrained('bills')->nullOnDelete();
            $table->string('type')->nullable(); // optional: jenis tagihan
            $table->unsignedBigInteger('amount');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('description')->nullable();
            $table->string('receipt_path')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran_kas');
    }
}
