<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('predictions', 'prediction_type')) {
            Schema::table('predictions', function ($table) {
                $table->string('prediction_type')->default('on track');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The original predictions table migration owns this column.
    }
};
