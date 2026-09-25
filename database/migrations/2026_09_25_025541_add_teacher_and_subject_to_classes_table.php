<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('created_by')
                ->nullable()
                ->after('class_code')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('subject_id')
                ->nullable()
                ->after('created_by')
                ->constrained('subjects')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['created_by']);

            $table->dropColumn([
                'subject_id',
                'created_by',
            ]);
        });
    }
};
