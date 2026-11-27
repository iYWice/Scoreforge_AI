<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE exam_attempts MODIFY status ENUM('ongoing', 'completed', 'timed_out') NOT NULL DEFAULT 'ongoing'");
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');

            DB::statement("CREATE TABLE exam_attempts_new_status (
                id integer primary key autoincrement not null,
                exam_id integer not null,
                student_id integer not null,
                score decimal(8, 2),
                total_score decimal(8, 2),
                submitted_at datetime,
                status text not null default 'ongoing' check(status in ('ongoing', 'completed', 'timed_out')),
                retake_allowed integer not null default 0,
                started_at datetime,
                created_at datetime,
                updated_at datetime,
                foreign key(exam_id) references exams(id) on delete cascade,
                foreign key(student_id) references users(id) on delete cascade
            )");

            DB::statement('INSERT INTO exam_attempts_new_status (id, exam_id, student_id, score, total_score, submitted_at, status, retake_allowed, started_at, created_at, updated_at)
                SELECT id, exam_id, student_id, score, total_score, submitted_at, status,
                    retake_allowed, started_at, created_at, updated_at
                FROM exam_attempts');

            DB::statement('DROP TABLE exam_attempts');
            DB::statement('ALTER TABLE exam_attempts_new_status RENAME TO exam_attempts');
            DB::statement('PRAGMA foreign_keys=ON');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("UPDATE exam_attempts SET status = 'ongoing' WHERE status = 'timed_out'");
            DB::statement("ALTER TABLE exam_attempts MODIFY status ENUM('ongoing', 'completed') NOT NULL DEFAULT 'ongoing'");
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');

            DB::statement("CREATE TABLE exam_attempts_new_status (
                id integer primary key autoincrement not null,
                exam_id integer not null,
                student_id integer not null,
                score decimal(8, 2),
                total_score decimal(8, 2),
                submitted_at datetime,
                status text not null default 'ongoing' check(status in ('ongoing', 'completed')),
                retake_allowed integer not null default 0,
                started_at datetime,
                created_at datetime,
                updated_at datetime,
                foreign key(exam_id) references exams(id) on delete cascade,
                foreign key(student_id) references users(id) on delete cascade
            )");

            DB::statement('INSERT INTO exam_attempts_new_status (id, exam_id, student_id, score, total_score, submitted_at, status, retake_allowed, started_at, created_at, updated_at)
                SELECT id, exam_id, student_id, score, total_score, submitted_at,
                    CASE WHEN status = \'timed_out\' THEN \'ongoing\' ELSE status END,
                    retake_allowed, started_at, created_at, updated_at
                FROM exam_attempts');

            DB::statement('DROP TABLE exam_attempts');
            DB::statement('ALTER TABLE exam_attempts_new_status RENAME TO exam_attempts');
            DB::statement('PRAGMA foreign_keys=ON');
        }
    }
};
