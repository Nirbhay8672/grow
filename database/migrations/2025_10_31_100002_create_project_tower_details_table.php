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
        Schema::create('project_tower_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('tower_name')->nullable();
            
            // Saleable Area
            $table->string('saleable_area_from')->nullable();
            $table->string('saleable_area_to')->nullable();
            $table->foreignId('saleable_area_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            // Ceiling Height
            $table->string('ceiling_height')->nullable();
            $table->foreignId('ceiling_height_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            // Carpet Area (optional)
            $table->boolean('show_carpet_area')->default(false);
            $table->string('carpet_area_from')->nullable();
            $table->string('carpet_area_to')->nullable();
            $table->foreignId('carpet_area_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            // Built-up Area (optional)
            $table->boolean('show_builtup_area')->default(false);
            $table->string('builtup_area_from')->nullable();
            $table->string('builtup_area_to')->nullable();
            $table->foreignId('builtup_area_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_tower_details');
    }
};

