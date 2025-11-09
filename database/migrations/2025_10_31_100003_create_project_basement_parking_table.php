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
        Schema::create('project_basement_parkings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('floor_no')->nullable();
            $table->string('ev_charging_point')->nullable();
            $table->string('hydraulic_parking')->nullable();
            $table->string('height_of_basement')->nullable();
            $table->foreignId('height_of_basement_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_basement_parkings');
    }
};

