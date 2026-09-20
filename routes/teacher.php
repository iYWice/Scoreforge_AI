<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\ExamController;
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\AnalyticsController;

Route::middleware(['auth', 'role:teacher'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    // Route::get('/teacher/dashboard', function () {

    //     return view('teacher.dashboard');
    // })->name('teacher.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Exams
    |--------------------------------------------------------------------------
    */

    Route::resource('teacher/exams', ExamController::class)->names([
        'index' => 'teacher.exams.index',
        'create' => 'teacher.exams.create',
        'store' => 'teacher.exams.store',
        'show' => 'teacher.exams.show',
        'edit' => 'teacher.exams.edit',
        'update' => 'teacher.exams.update',
        'destroy' => 'teacher.exams.destroy',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Question Builder
    |--------------------------------------------------------------------------
    */
    Route::get(
        '/teacher/analytics/predictions',
        [AnalyticsController::class, 'predictions']
    )->name('teacher.analytics.predictions');

    Route::get(
        '/teacher/analytics',
        [AnalyticsController::class, 'index']
    )->name('teacher.analytics.index');

    Route::get(
        '/teacher/analytics/exam/{exam}',
        [AnalyticsController::class, 'exam']
    )->name('teacher.analytics.exam');

    Route::get(
        '/teacher/exams/{exam}/questions',
        [QuestionController::class, 'index']
    )->name('teacher.exams.questions');

    Route::get(
        '/teacher/exams/{exam}/questions/create',
        [QuestionController::class, 'create']
    )->name('teacher.questions.create');

    Route::post(
        '/teacher/exams/{exam}/questions',
        [QuestionController::class, 'store']
    )->name('teacher.questions.store');

    /*
    |--------------------------------------------------------------------------
    | Question CRUD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/teacher/questions/{question}/edit',
        [QuestionController::class, 'edit']
    )->name('teacher.questions.edit');

    Route::put(
        '/teacher/questions/{question}',
        [QuestionController::class, 'update']
    )->name('teacher.questions.update');

    Route::delete(
        '/teacher/questions/{question}',
        [QuestionController::class, 'destroy']
    )->name('teacher.questions.destroy');


    Route::patch(
        '/teacher/exams/{exam}/status',
        [ExamController::class, 'updateStatus']
    )->name('teacher.exams.status');

    Route::post(
        '/teacher/analytics/exam/{exam}/ai-insight',
        [AnalyticsController::class, 'generateAiInsight']
    )->name('teacher.analytics.ai-insight');

    Route::post(
        '/teacher/analytics/exam/{exam}/ai-insight/regenerate',
        [AnalyticsController::class, 'regenerateAiInsight']
    )->name('teacher.analytics.ai-insight.regenerate');
});
