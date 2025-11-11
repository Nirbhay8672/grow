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
        // Create project_category3_unit_details table
        Schema::create('project_category3_unit_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            
            // Total No. Of Units (First field in form)
            $table->string('total_no_of_units')->nullable();
            
            // Ceiling Height (Second field in form)
            $table->string('ceiling_height')->nullable();
            $table->foreignId('ceiling_height_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            // Plot Area (Third field in form - first size field)
            $table->string('plot_area_from')->nullable();
            $table->string('plot_area_to')->nullable();
            $table->foreignId('plot_area_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            // Road Width Of Front Side Area (Fourth field in form - second size field)
            $table->string('road_width_from')->nullable();
            $table->string('road_width_to')->nullable();
            $table->foreignId('road_width_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            // Constructed Area (Last field in form - third size field, hidden when sub_category_id === 9)
            $table->string('constructed_area_from')->nullable();
            $table->string('constructed_area_to')->nullable();
            $table->foreignId('constructed_area_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            $table->timestamps();
        });

        // Add category3_utility_board and category3_dynamic_facilities columns to projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->json('category3_utility_board')->nullable()->after('retail_unit_details');
            $table->json('category3_dynamic_facilities')->nullable()->after('category3_utility_board');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_category3_unit_details');
        
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['category3_utility_board', 'category3_dynamic_facilities']);
        });
    }
};

