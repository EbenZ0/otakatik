<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ambil nama schema dari konfigurasi koneksi
        $schema = config('database.connections.oracle.schema', 'YOUR_SCHEMA_NAME');

        // Pastikan schema tidak kosong
        if (empty($schema)) {
            throw new \Exception('Oracle schema name is not configured');
        }

        // Gunakan raw SQL dengan schema yang jelas
        DB::statement("CREATE TABLE \"{$schema}\".\"ASSIGNMENT_SUBMISSIONS\" (
            \"ID\" NUMBER(19,0) NOT NULL PRIMARY KEY,
            \"ASSIGNMENT_ID\" NUMBER(19,0) NOT NULL,
            \"USER_ID\" NUMBER(19,0) NOT NULL,
            \"FILE_PATH\" VARCHAR2(255),
            \"FILE_NAME\" VARCHAR2(255),
            \"SUBMISSION_TEXT\" CLOB,
            \"SUBMITTED_AT\" TIMESTAMP NOT NULL,
            \"GRADE\" NUMBER(5,2),
            \"FEEDBACK\" CLOB,
            \"CREATED_AT\" TIMESTAMP,
            \"UPDATED_AT\" TIMESTAMP,
            CONSTRAINT \"ASSIGNMENT_SUBMISSIONS_ASSIGNMENT_ID_FK\" 
                FOREIGN KEY (\"ASSIGNMENT_ID\") 
                REFERENCES \"{$schema}\".\"COURSE_ASSIGNMENTS\" (\"ID\") 
                ON DELETE CASCADE,
            CONSTRAINT \"ASSIGNMENT_SUBMISSIONS_USER_ID_FK\" 
                FOREIGN KEY (\"USER_ID\") 
                REFERENCES \"{$schema}\".\"USERS\" (\"ID\") 
                ON DELETE CASCADE
        )");

        // Tambahkan sequence untuk ID
        DB::statement("CREATE SEQUENCE \"{$schema}\".\"ASSIGNMENT_SUBMISSIONS_SEQ\" 
            START WITH 1 
            INCREMENT BY 1 
            NOMAXVALUE 
            NOCACHE
            NOCYCLE");
    }

    public function down(): void
    {
        $schema = config('database.connections.oracle.schema', 'YOUR_SCHEMA_NAME');

        if (empty($schema)) {
            throw new \Exception('Oracle schema name is not configured');
        }

        DB::statement("DROP TABLE \"{$schema}\".\"ASSIGNMENT_SUBMISSIONS\"");
        DB::statement("DROP SEQUENCE \"{$schema}\".\"ASSIGNMENT_SUBMISSIONS_SEQ\"");
    }
};