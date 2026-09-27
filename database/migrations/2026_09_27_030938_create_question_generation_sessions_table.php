<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_generation_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('exam_id')
                ->nullable()
                ->constrained('exams')
                ->cascadeOnDelete();

            $table->string('source_name');

            $table->string('source_file')
                ->nullable();

            $table->string('purpose')
                ->default('exam');

            $table->unsignedInteger('question_count');

            $table->json('question_types');

            $table->string('difficulty')
                ->nullable();

            $table->json('generated_questions')
                ->nullable();

            $table->string('status')
                ->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'question_generation_sessions'
        );
    }
};
