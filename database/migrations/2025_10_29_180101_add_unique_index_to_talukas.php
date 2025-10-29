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
        Schema::table('talukas', function (Blueprint $table) {
            $table->unique(['name', 'district_id'], 'talukas_name_district_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talukas', function (Blueprint $table) {
            $table->dropUnique('talukas_name_district_id_unique');
        });
    }
};

