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
        Schema::table('organizations', function (Blueprint $table) {

            if (!Schema::hasColumn('organizations', 'short_name')) {
                $table->string('short_name')->nullable();
            }

            if (!Schema::hasColumn('organizations', 'email')) {
                $table->string('email')->nullable();
            }

            if (!Schema::hasColumn('organizations', 'phone')) {
                $table->string('phone')->nullable();
            }

            if (!Schema::hasColumn('organizations', 'address')) {
                $table->text('address')->nullable();
            }

            if (!Schema::hasColumn('organizations', 'logo_path')) {
                $table->string('logo_path')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            //
        });
    }
};
