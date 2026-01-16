<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('ewallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('provider');      // gopay, ovo, dana
            $table->string('number');        // no hp
            $table->string('owner_name');    // atas nama
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ewallets');
    }
};


