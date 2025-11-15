<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            // Add payment related fields
            $table->string('order_id')->nullable()->after('id');
            $table->string('payment_method')->nullable()->after('discount_code');
            $table->timestamp('paid_at')->nullable()->after('completed_at');
            $table->text('payment_notes')->nullable()->after('paid_at');
            
            // Make final_price nullable since it will be calculated at checkout
            $table->decimal('final_price', 10, 2)->nullable()->change();
            
            // Add indexes
            $table->index('order_id');
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'payment_method', 'paid_at', 'payment_notes']);
            $table->decimal('final_price', 10, 2)->nullable(false)->change();
        });
    }
};