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
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('two_road_corner')->default(false)->after('washroom');
            $table->json('retail_unit_details')->nullable()->after('two_road_corner');
            $table->json('sub_category_ids')->nullable()->after('sub_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['two_road_corner', 'retail_unit_details', 'sub_category_ids']);
        });
    }
};

