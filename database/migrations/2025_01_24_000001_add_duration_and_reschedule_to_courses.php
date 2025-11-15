<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('duration_days')->default(30)->after('max_quota'); // Durasi kursus dalam hari
            $table->date('start_date')->nullable()->after('duration_days');
            $table->date('end_date')->nullable()->after('start_date');
            $table->boolean('is_rescheduled')->default(false)->after('is_active');
            $table->date('rescheduled_start_date')->nullable()->after('is_rescheduled');
            $table->string('reschedule_reason')->nullable()->after('rescheduled_start_date');
            $table->boolean('quota_not_met')->default(false)->after('reschedule_reason');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'duration_days', 
                'start_date', 
                'end_date', 
                'is_rescheduled', 
                'rescheduled_start_date', 
                'reschedule_reason',
                'quota_not_met'
            ]);
        });
    }
};