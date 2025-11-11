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
        // Create project_category5_tower_details table
        Schema::create('project_category5_tower_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('tower_name')->nullable();
            $table->string('total_units')->nullable();
            $table->string('total_floor')->nullable();
            $table->json('sub_category_ids')->nullable();
            $table->timestamps();
        });

        // Create project_category5_unit_details table
        Schema::create('project_category5_unit_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('tower_name')->nullable();
            
            // Saleable Area
            $table->string('saleable_from')->nullable();
            $table->string('saleable_to')->nullable();
            $table->foreignId('saleable_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            // Wash Area
            $table->string('wash_area')->nullable();
            $table->foreignId('wash_area_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            // Balcony Area
            $table->string('balcony_area')->nullable();
            $table->foreignId('balcony_area_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            // Ceiling Height
            $table->string('ceiling_height')->nullable();
            $table->foreignId('ceiling_height_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            // Servant Room
            $table->boolean('servant_room')->default(false);
            
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

        // Add category5_total_room to projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->string('category5_total_room')->nullable()->after('category4_total_room');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_category5_unit_details');
        Schema::dropIfExists('project_category5_tower_details');
        
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('category5_total_room');
        });
    }
};

