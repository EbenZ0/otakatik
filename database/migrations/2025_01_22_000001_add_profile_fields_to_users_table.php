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
            $table->string('age_range')->nullable()->after('is_admin');
            $table->string('education_level')->nullable()->after('age_range');
            $table->string('location')->nullable()->after('education_level');
            $table->string('phone')->nullable()->after('location');
            $table->date('date_of_birth')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['age_range', 'education_level', 'location', 'phone', 'date_of_birth']);
        });
    }
};