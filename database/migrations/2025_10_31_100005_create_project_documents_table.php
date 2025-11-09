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
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('category')->nullable(); // Fact sheet, Area sheet, Price sheet, Images, Other
            $table->string('file_path'); // Path to the uploaded file
            $table->string('file_name'); // Original file name
            $table->string('file_type')->nullable(); // MIME type
            $table->unsignedBigInteger('file_size')->nullable(); // File size in bytes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};

