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
        Schema::create('ai_insights', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_id')
                ->constrained('exams')
                ->cascadeOnDelete();

            $table->foreignId('generated_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('type')->default('teacher_exam');

            $table->text('performance_summary');

            $table->json('key_findings')->nullable();

            $table->json('recommended_actions')->nullable();

            $table->string('model')->nullable();

            $table->timestamp('generated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_insights');
    }
};
