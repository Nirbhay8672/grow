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
        Schema::table('localities', function (Blueprint $table) {
            $table->unique(['state_id', 'city_id', 'zip_code'], 'localities_state_city_zip_unique');
            $table->unique('zip_code', 'localities_zip_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('localities', function (Blueprint $table) {
            $table->dropUnique('localities_state_city_zip_unique');
            $table->dropUnique('localities_zip_code_unique');
        });
    }
};

