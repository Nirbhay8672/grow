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
        // Create project_office_retail_data table
        Schema::create('project_office_retail_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            
            // Office Sub Category
            $table->unsignedBigInteger('office_sub_category_id')->nullable();
            $table->foreign('office_sub_category_id', 'po_retail_data_office_sub_cat_fk')->references('id')->on('sub_categories')->onDelete('set null');
            
            // Tower Details
            $table->string('no_of_towers')->nullable();
            $table->string('no_of_floors')->nullable();
            $table->string('no_of_unit_each_tower')->nullable();
            $table->string('no_of_lift')->nullable();
            $table->string('front_road_width')->nullable();
            $table->unsignedBigInteger('front_road_width_unit_id')->nullable();
            $table->foreign('front_road_width_unit_id', 'po_retail_data_frw_unit_fk')->references('id')->on('measurement_units')->onDelete('set null');
            
            // Washroom
            $table->string('washroom')->nullable();
            $table->boolean('two_road_corner')->default(false);
            
            // Tower Name and Total Units
            $table->string('tower_name')->nullable();
            $table->string('total_units')->nullable();
            
            // Saleable Area
            $table->string('saleable_from')->nullable();
            $table->string('saleable_to')->nullable();
            $table->unsignedBigInteger('saleable_unit_id')->nullable();
            $table->foreign('saleable_unit_id', 'po_retail_data_saleable_unit_fk')->references('id')->on('measurement_units')->onDelete('set null');
            
            // Carpet Area (optional)
            $table->boolean('show_carpet_area')->default(false);
            $table->string('carpet_area_from')->nullable();
            $table->string('carpet_area_to')->nullable();
            $table->unsignedBigInteger('carpet_area_unit_id')->nullable();
            $table->foreign('carpet_area_unit_id', 'po_retail_data_carpet_unit_fk')->references('id')->on('measurement_units')->onDelete('set null');
            
            // BuiltUp Area (optional)
            $table->boolean('show_builtup_area')->default(false);
            $table->string('builtup_area_from')->nullable();
            $table->string('builtup_area_to')->nullable();
            $table->unsignedBigInteger('builtup_area_unit_id')->nullable();
            $table->foreign('builtup_area_unit_id', 'po_retail_data_builtup_unit_fk')->references('id')->on('measurement_units')->onDelete('set null');
            
            $table->timestamps();
        });

        // Create project_office_retail_retail_unit_details table
        Schema::create('project_office_retail_retail_unit_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            
            // Retail Unit Details
            $table->string('tower_name')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->foreign('sub_category_id', 'po_retail_unit_sub_cat_fk')->references('id')->on('sub_categories')->onDelete('set null');
            $table->string('size_from')->nullable();
            $table->string('size_to')->nullable();
            $table->unsignedBigInteger('size_unit_id')->nullable();
            $table->foreign('size_unit_id', 'po_retail_unit_size_unit_fk')->references('id')->on('measurement_units')->onDelete('set null');
            $table->string('front_opening')->nullable();
            $table->unsignedBigInteger('front_opening_unit_id')->nullable();
            $table->foreign('front_opening_unit_id', 'po_retail_unit_front_unit_fk')->references('id')->on('measurement_units')->onDelete('set null');
            $table->string('no_of_unit_each_floor')->nullable();
            $table->string('ceiling_height')->nullable();
            $table->unsignedBigInteger('ceiling_height_unit_id')->nullable();
            $table->foreign('ceiling_height_unit_id', 'po_retail_unit_ceiling_unit_fk')->references('id')->on('measurement_units')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_office_retail_retail_unit_details');
        Schema::dropIfExists('project_office_retail_data');
    }
};

