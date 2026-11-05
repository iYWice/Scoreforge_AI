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
        Schema::table('predictions', function (Blueprint $table) {
            $table->foreignId('subject_id')
                ->nullable()
                ->constrained('subjects')
                ->nullOnDelete();

            $table->decimal('predicted_score', 5, 2)->nullable();
            $table->string('predicted_level')->nullable();
            $table->string('risk_level')->nullable();
            $table->string('confidence_level')->nullable();
            $table->text('reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn([
                'subject_id',
                'predicted_score',
                'predicted_level',
                'risk_level',
                'confidence_level',
                'reason',
            ]);
        });
    }
};
