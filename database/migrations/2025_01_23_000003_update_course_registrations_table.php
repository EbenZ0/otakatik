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
        Schema::table('course_registrations', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['course', 'sub_course1', 'sub_course2', 'kelas']);
            
            // Add new columns
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2)->change();
            $table->decimal('final_price', 10, 2);
            $table->string('discount_code')->nullable();
            $table->decimal('progress', 5, 2)->default(0);
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            // Add back old columns
            $table->enum('course', ['Starter', 'Pro Learner', 'Expert Mode']);
            $table->string('sub_course1');
            $table->string('sub_course2');
            $table->string('kelas');
            
            // Remove new columns
            $table->dropForeign(['course_id']);
            $table->dropColumn(['course_id', 'final_price', 'discount_code', 'progress', 'enrolled_at', 'completed_at']);
            
            // Revert price type
            $table->integer('price')->change();
        });
    }
};