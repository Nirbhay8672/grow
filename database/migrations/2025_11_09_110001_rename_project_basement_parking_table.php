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
        // Rename table if it exists with the old name
        if (Schema::hasTable('project_basement_parking') && !Schema::hasTable('project_basement_parkings')) {
            Schema::rename('project_basement_parking', 'project_basement_parkings');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename back if needed
        if (Schema::hasTable('project_basement_parkings') && !Schema::hasTable('project_basement_parking')) {
            Schema::rename('project_basement_parkings', 'project_basement_parking');
        }
    }
};

