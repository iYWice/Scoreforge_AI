<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('edp_code', 50)->unique();
            $table->string('schedule', 255);
            $table->timestamps();
        });

        Schema::create('subject_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_subject_id')
                ->constrained('class_subjects')
                ->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique(['class_subject_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_enrollments');
        Schema::dropIfExists('class_subjects');
    }
};