<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('status')->default('aktif')->index();
            $table->timestamp('last_activity')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nim', 'phone', 'status', 'last_activity']);
        });
    }
};
