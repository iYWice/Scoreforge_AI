<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\ExamController;
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\AnalyticsController;
use App\Http\Controllers\Teacher\ClassController;
use App\Http\Controllers\Teacher\AIQuestionBuilderController;
use App\Http\Controllers\Teacher\ClassSubjectController;
use App\Http\Controllers\Teacher\StudentPerformanceController;

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


    Route::get(
        '/teacher/classes',
        [ClassController::class, 'index']
    )->name('teacher.classes.index');

    Route::post(
        '/teacher/classes',
        [ClassController::class, 'store']
    )->name('teacher.classes.store');

    Route::get(
        '/teacher/classes/{class}',
        [ClassController::class, 'show']
    )->name('teacher.classes.show');

    Route::patch(
        '/teacher/classes/{class}',
        [ClassController::class, 'update']
    )->name('teacher.classes.update');

    Route::get(
        '/teacher/exams/{exam}/ai-question-builder',
        [AIQuestionBuilderController::class, 'create']
    )->name('teacher.ai-questions.create');


    Route::post(
        '/teacher/exams/{exam}/ai-question-builder',
        [AIQuestionBuilderController::class, 'generate']
    )->name('teacher.ai-questions.generate');


    Route::get(
        '/teacher/exams/{exam}/ai-question-builder/{session}',
        [AIQuestionBuilderController::class, 'review']
    )->name('teacher.ai-questions.review');

    Route::post(
        '/teacher/exams/{exam}/ai-question-builder/{session}',
        [AIQuestionBuilderController::class, 'commit']
    )->name('teacher.ai-questions.commit');

    Route::put(
        '/teacher/exams/{exam}/ai-question-builder/{session}/questions/{questionIndex}',
        [AIQuestionBuilderController::class, 'updateGeneratedQuestion']
    )->whereNumber('questionIndex')
        ->name('teacher.ai-questions.update-question');

    Route::get(
        '/teacher/analytics/export',
        [AnalyticsController::class, 'export']
    )->name('teacher.analytics.export');

    Route::get(
        '/teacher/analytics/predictions/export',
        [AnalyticsController::class, 'exportPredictions']
    )->name('teacher.analytics.predictions.export');

    Route::post(
    '/teacher/classes/{class}/subjects',
    [ClassSubjectController::class, 'store']
)->name('teacher.classes.subjects.store');

Route::post(
    '/teacher/class-subjects/{classSubject}/students',
    [ClassSubjectController::class, 'assignStudents']
)->name('teacher.class-subjects.students.assign');

Route::get(
    '/teacher/students/{student}/performance',
    [StudentPerformanceController::class, 'show']
)->name('teacher.students.performance');

Route::post(
    '/teacher/students/{student}/attempts/{attempt}/analysis',
    [StudentPerformanceController::class, 'storeExam']
)->name('teacher.students.analysis.exam');

Route::post(
    '/teacher/students/{student}/analysis/overall',
    [StudentPerformanceController::class, 'storeOverall']
)->name('teacher.students.analysis.overall');

Route::patch(
    '/teacher/performance-analyses/{analysis}',
    [StudentPerformanceController::class, 'updateDraft']
)->name('teacher.performance-analyses.update');

Route::post(
    '/teacher/performance-analyses/{analysis}/send',
    [StudentPerformanceController::class, 'send']
)->name('teacher.performance-analyses.send');
Route::get(
    '/teacher/student-attempts/{attempt}/result',
    [StudentPerformanceController::class, 'showResult']
)->name('teacher.students.attempt-result');
Route::post(
    '/teacher/students/{student}/attempts/{attempt}/analysis/ai-draft',
    [StudentPerformanceController::class, 'generateExamDraft']
)->name('teacher.students.analysis.exam.generate');
Route::post(
    '/teacher/students/{student}/analysis/overall/ai-draft',
    [StudentPerformanceController::class, 'generateOverallDraft']
)->name('teacher.students.analysis.overall.generate');
});
