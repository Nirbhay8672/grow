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
            $table->foreignId('builder_id')->constrained()->onDelete('cascade');
            $table->string('builder_website')->nullable();
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

