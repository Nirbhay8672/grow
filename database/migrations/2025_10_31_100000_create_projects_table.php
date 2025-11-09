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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            // Step 1: Builder Information
            $table->foreignId('builder_id')->constrained()->onDelete('cascade');
            $table->string('builder_website')->nullable();
            
            // Step 1: Project Information
            $table->string('project_name');
            $table->text('address')->nullable();
            $table->foreignId('state_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('locality_id')->constrained()->onDelete('restrict');
            $table->string('pincode')->nullable();
            $table->string('location_link')->nullable();
            $table->string('land_size')->nullable();
            $table->foreignId('measurement_unit_id')->nullable()->constrained()->onDelete('set null');
            $table->string('rera_no')->nullable();
            $table->string('project_status')->nullable();
            $table->json('restricted_user_ids')->nullable();
            
            // Step 2: Construction Type & Category
            $table->foreignId('construction_type_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->onDelete('set null');
            
            // Step 2: Tower Details (Common fields - detailed specs in project_tower_details table)
            $table->string('no_of_towers')->nullable();
            $table->string('no_of_floors')->nullable();
            $table->string('total_units')->nullable();
            $table->string('no_of_unit_each_tower')->nullable();
            $table->string('no_of_lift')->nullable();
            $table->string('front_road_width')->nullable();
            $table->foreignId('front_road_width_measurement_unit_id')->nullable()->constrained('measurement_units')->onDelete('set null');
            $table->string('washroom')->default('Private'); // Private, Shared, etc.
            $table->boolean('towers_different_specification')->default(false);
            
            // Step 3: Parking Details
            $table->boolean('free_allotted_parking_four_wheeler')->default(false);
            $table->boolean('free_allotted_parking_two_wheeler')->default(false);
            $table->boolean('available_for_purchase')->default(false);
            $table->string('no_of_parking')->nullable();
            $table->string('total_floor_for_parking')->default('1');
            
            // Step 3: Other
            $table->text('remark')->nullable();
            $table->string('brochure_file')->nullable(); // File path for brochure
            
            // Common fields
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

