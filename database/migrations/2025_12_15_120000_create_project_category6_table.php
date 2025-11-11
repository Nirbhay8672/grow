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
        // Create project_category6_data table
        Schema::create('project_category6_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            
            // General Details
            $table->string('total_open_area')->nullable();
            $table->foreignId('total_open_area_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            $table->string('total_no_of_plots')->nullable();
            
            // Checkboxes
            $table->boolean('project_with_multiple_theme_phase')->default(false);
            $table->string('phase_name')->nullable();
            $table->boolean('plots_with_construction')->default(false);
            
            // Saleable Plot Size
            $table->string('saleable_plot_from')->nullable();
            $table->string('saleable_plot_to')->nullable();
            $table->foreignId('saleable_plot_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            // Carpet Plot Size (optional)
            $table->boolean('show_carpet_plot_size')->default(false);
            $table->string('carpet_plot_from')->nullable();
            $table->string('carpet_plot_to')->nullable();
            $table->foreignId('carpet_plot_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            // Constructed Saleable Area (optional)
            $table->string('constructed_saleable_area_from')->nullable();
            $table->string('constructed_saleable_area_to')->nullable();
            $table->foreignId('constructed_saleable_area_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_category6_data');
    }
};

