<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('duration_days')->default(30);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_rescheduled')->default(false);
            $table->date('rescheduled_start_date')->nullable();
            $table->string('reschedule_reason')->nullable();
            $table->boolean('quota_not_met')->default(false);
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