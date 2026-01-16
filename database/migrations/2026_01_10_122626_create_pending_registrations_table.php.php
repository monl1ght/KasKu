<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pending_registrations', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();

            // simpan hash password (bukan plain)
            $table->string('password');

            // token disimpan dalam bentuk hash
            $table->string('token_hash', 64);

            // masa berlaku link
            $table->timestamp('expires_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_registrations');
    }
};
