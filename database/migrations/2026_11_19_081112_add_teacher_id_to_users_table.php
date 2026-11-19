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
            if (! Schema::hasColumn('users', 'teacher_id')) {
                $table->string('teacher_id', 50)->nullable()->unique()->after('student_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'teacher_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['teacher_id']);
                $table->dropColumn('teacher_id');
            });
        }
    }
};
