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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('email');
            $table->string('first_name')->after('name');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->after('middle_name');
            $table->string('mobile')->nullable()->after('last_name');
            $table->string('company_name')->nullable()->after('mobile');
            $table->date('birth_date')->nullable()->after('company_name');
            $table->foreignId('state_id')->nullable()->constrained()->nullOnDelete()->after('birth_date');
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete()->after('state_id');
            $table->boolean('is_active')->default(true)->after('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn([
                'username',
                'first_name',
                'middle_name',
                'last_name',
                'mobile',
                'company_name',
                'birth_date',
                'state_id',
                'city_id',
                'is_active',
            ]);
        });
    }
};
