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
            if (! Schema::hasColumn('users', 'fname')) {
                $table->string('fname')->nullable()->after('id');
            }

            if (! Schema::hasColumn('users', 'lname')) {
                $table->string('lname')->nullable()->after('fname');
            }

            if (! Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id', 50)->nullable()->unique()->after('role');
            }
        });

        if (Schema::hasColumn('users', 'name') && Schema::hasColumn('users', 'fname')) {
            $users = \Illuminate\Support\Facades\DB::table('users')
                ->where(function ($query) {
                    $query->whereNull('fname')
                        ->orWhereNull('lname')
                        ->orWhere('fname', '')
                        ->orWhere('lname', '');
                })
                ->get(['id', 'name', 'fname', 'lname']);

            foreach ($users as $user) {
                $parts = preg_split('/\s+/', trim((string) $user->name)) ?: [];
                $fname = $parts[0] ?? '';
                $lname = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

                \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'fname' => $user->fname ?: $fname,
                        'lname' => $user->lname ?: $lname,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
