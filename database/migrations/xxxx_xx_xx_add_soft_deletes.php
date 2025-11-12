<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kosongkan method ini karena kolom sudah dibuat di migration sebelumnya
        // Schema::table('users', function (Blueprint $table) {
        //     $table->softDeletes();
        // });
    }

    public function down(): void
    {
        // Juga kosongkan method down
    }
};