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
        Schema::create('exam_answers', function (Blueprint $table) {

            $table->id();

            $table->foreignId('attempt_id')
                ->constrained('exam_attempts')
                ->onDelete('cascade');

            $table->foreignId('question_id')
                ->constrained()
                ->onDelete('cascade');

            $table->text('student_answer');

            $table->boolean('is_correct')
                ->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
