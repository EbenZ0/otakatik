    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Facades\DB;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::table('course_registrations', function (Blueprint $table) {
                // Check dan drop kolom lama jika ada
                if (Schema::hasColumn('course_registrations', 'course')) {
                    $table->dropColumn(['course', 'sub_course1', 'sub_course2', 'kelas']);
                }
                
                // Add new columns
                if (!Schema::hasColumn('course_registrations', 'course_id')) {
                    $table->foreignId('course_id')->constrained()->onDelete('cascade');
                }
                
                if (!Schema::hasColumn('course_registrations', 'final_price')) {
                    $table->decimal('final_price', 10, 2);
                }
                
                if (!Schema::hasColumn('course_registrations', 'discount_code')) {
                    $table->string('discount_code')->nullable();
                }
                
                if (!Schema::hasColumn('course_registrations', 'progress')) {
                    $table->decimal('progress', 5, 2)->default(0);
                }
                
                if (!Schema::hasColumn('course_registrations', 'enrolled_at')) {
                    $table->timestamp('enrolled_at')->nullable();
                }
                
                if (!Schema::hasColumn('course_registrations', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable();
                }
            });

            // Fix price column tipe data
            try {
                $columnInfo = DB::select("
                    SELECT DATA_TYPE, DATA_PRECISION, DATA_SCALE, NULLABLE 
                    FROM USER_TAB_COLUMNS 
                    WHERE TABLE_NAME = 'COURSE_REGISTRATIONS' AND COLUMN_NAME = 'PRICE'
                ");
                
                if (!empty($columnInfo) && ($columnInfo[0]->data_precision != 10 || $columnInfo[0]->data_scale != 2)) {
                    DB::statement('ALTER TABLE "COURSE_REGISTRATIONS" MODIFY "PRICE" NUMBER(10, 2)');
                }
            } catch (Exception $e) {
                // Skip jika sudah benar
            }
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
                if (Schema::hasColumn('course_registrations', 'course_id')) {
                    $table->dropForeign(['course_id']);
                    $table->dropColumn('course_id');
                }
                
                $table->dropColumn(['final_price', 'discount_code', 'progress', 'enrolled_at', 'completed_at']);
                
                // Revert price type
                DB::statement('ALTER TABLE "COURSE_REGISTRATIONS" MODIFY "PRICE" NUMBER(10, 0)');
            });
        }
    };