<?php

use App\Http\Controllers\Teacher\AIQuestionBuilderController;
use App\Http\Controllers\Teacher\AnalyticsController;
use App\Http\Controllers\Teacher\ClassController;
use App\Http\Controllers\Teacher\ClassSubjectController;
use App\Http\Controllers\Teacher\ExamController;
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\StudentPerformanceController;
use Illuminate\Support\Facades\Route;

// Apply the shared middleware, prefix, and name scope to this route group.
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

    // Define this application endpoint.
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
    // Define this application endpoint.
    Route::get(
        '/teacher/analytics/predictions',
        [AnalyticsController::class, 'predictions']
    )->name('teacher.analytics.predictions');

    // Define this application endpoint.
    Route::get(
        '/teacher/analytics',
        [AnalyticsController::class, 'index']
    )->name('teacher.analytics.index');

    // Define this application endpoint.
    Route::get(
        '/teacher/analytics/exam/{exam}',
        [AnalyticsController::class, 'exam']
    )->name('teacher.analytics.exam');

    // Define this application endpoint.
    Route::get(
        '/teacher/exams/{exam}/questions',
        [QuestionController::class, 'index']
    )->name('teacher.exams.questions');

    // Define this application endpoint.
    Route::get(
        '/teacher/exams/{exam}/questions/create',
        [QuestionController::class, 'create']
    )->name('teacher.questions.create');

    // Define this application endpoint.
    Route::post(
        '/teacher/exams/{exam}/questions',
        [QuestionController::class, 'store']
    )->name('teacher.questions.store');

    /*
    |--------------------------------------------------------------------------
    | Question CRUD
    |--------------------------------------------------------------------------
    */

    // Define this application endpoint.
    Route::get(
        '/teacher/questions/{question}/edit',
        [QuestionController::class, 'edit']
    )->name('teacher.questions.edit');

    // Define this application endpoint.
    Route::put(
        '/teacher/questions/{question}',
        [QuestionController::class, 'update']
    )->name('teacher.questions.update');

    // Define this application endpoint.
    Route::delete(
        '/teacher/questions/{question}',
        [QuestionController::class, 'destroy']
    )->name('teacher.questions.destroy');

    // Define this application endpoint.
    Route::patch(
        '/teacher/exams/{exam}/status',
        [ExamController::class, 'updateStatus']
    )->name('teacher.exams.status');

    // Define this application endpoint.
    Route::post(
        '/teacher/analytics/exam/{exam}/ai-insight',
        [AnalyticsController::class, 'generateAiInsight']
    )->name('teacher.analytics.ai-insight');

    // Define this application endpoint.
    Route::post(
        '/teacher/analytics/exam/{exam}/ai-insight/regenerate',
        [AnalyticsController::class, 'regenerateAiInsight']
    )->name('teacher.analytics.ai-insight.regenerate');

    // Define this application endpoint.
    Route::get(
        '/teacher/classes',
        [ClassController::class, 'index']
    )->name('teacher.classes.index');

    // Define this application endpoint.
    Route::post(
        '/teacher/classes',
        [ClassController::class, 'store']
    )->name('teacher.classes.store');

    // Define this application endpoint.
    Route::get(
        '/teacher/classes/{class}',
        [ClassController::class, 'show']
    )->name('teacher.classes.show');

    // Define this application endpoint.
    Route::patch(
        '/teacher/classes/{class}',
        [ClassController::class, 'update']
    )->name('teacher.classes.update');

    // Define this application endpoint.
    Route::get(
        '/teacher/exams/{exam}/ai-question-builder',
        [AIQuestionBuilderController::class, 'create']
    )->name('teacher.ai-questions.create');

    // Define this application endpoint.
    Route::post(
        '/teacher/exams/{exam}/ai-question-builder',
        [AIQuestionBuilderController::class, 'generate']
    )->name('teacher.ai-questions.generate');

    // Define this application endpoint.
    Route::get(
        '/teacher/exams/{exam}/ai-question-builder/{session}',
        [AIQuestionBuilderController::class, 'review']
    )->name('teacher.ai-questions.review');

    // Define this application endpoint.
    Route::post(
        '/teacher/exams/{exam}/ai-question-builder/{session}',
        [AIQuestionBuilderController::class, 'commit']
    )->name('teacher.ai-questions.commit');

    // Define this application endpoint.
    Route::put(
        '/teacher/exams/{exam}/ai-question-builder/{session}/questions/{questionIndex}',
        [AIQuestionBuilderController::class, 'updateGeneratedQuestion']
    )->whereNumber('questionIndex')
        ->name('teacher.ai-questions.update-question');

    // Define this application endpoint.
    Route::get(
        '/teacher/analytics/export',
        [AnalyticsController::class, 'export']
    )->name('teacher.analytics.export');

    // Define this application endpoint.
    Route::get(
        '/teacher/analytics/predictions/export',
        [AnalyticsController::class, 'exportPredictions']
    )->name('teacher.analytics.predictions.export');

    // Define this application endpoint.
    Route::post(
        '/teacher/classes/{class}/subjects',
        [ClassSubjectController::class, 'storeOffering']
    )->name('teacher.classes.subjects.store');

    // Define this application endpoint.
    Route::post(
        '/teacher/class-subjects/{classSubject}/students',
        [ClassSubjectController::class, 'assignStudents']
    )->name('teacher.class-subjects.students.assign');

    // Define this application endpoint.
    Route::get(
        '/teacher/students/{student}/performance',
        [StudentPerformanceController::class, 'show']
    )->name('teacher.students.performance');

    // Define this application endpoint.
    Route::post(
        '/teacher/students/{student}/attempts/{attempt}/analysis',
        [StudentPerformanceController::class, 'storeExam']
    )->name('teacher.students.analysis.exam');

    // Define this application endpoint.
    Route::post(
        '/teacher/students/{student}/analysis/overall',
        [StudentPerformanceController::class, 'storeOverall']
    )->name('teacher.students.analysis.overall');

    // Define this application endpoint.
    Route::patch(
        '/teacher/performance-analyses/{analysis}',
        [StudentPerformanceController::class, 'updateDraft']
    )->name('teacher.performance-analyses.update');

    // Define this application endpoint.
    Route::post(
        '/teacher/performance-analyses/{analysis}/send',
        [StudentPerformanceController::class, 'send']
    )->name('teacher.performance-analyses.send');
    // Define this application endpoint.
    Route::get(
        '/teacher/student-attempts/{attempt}/result',
        [StudentPerformanceController::class, 'showResult']
    )->name('teacher.students.attempt-result');
    // Define this application endpoint.
    Route::post(
        '/teacher/students/{student}/attempts/{attempt}/analysis/ai-draft',
        [StudentPerformanceController::class, 'generateExamDraft']
    )->name('teacher.students.analysis.exam.generate');
    // Define this application endpoint.
    Route::post(
        '/teacher/students/{student}/analysis/overall/ai-draft',
        [StudentPerformanceController::class, 'generateOverallDraft']
    )->name('teacher.students.analysis.overall.generate');
    // Define this application endpoint.
    Route::post(
        '/teacher/student-attempts/{attempt}/reset',
        [StudentPerformanceController::class, 'resetAttempt']
    )->name('teacher.students.attempts.reset');
    // Define this application endpoint.
    Route::delete(
        '/teacher/classes/{class}/students/{student}',
        [ClassController::class, 'removeStudent']
    )->name('teacher.classes.students.remove');
    // Define this application endpoint.
    Route::get(
        '/teacher/subjects',
        [ClassSubjectController::class, 'teacherSubjects']
    )->name('teacher.subjects.index');

    // Define this application endpoint.
    Route::post(
        '/teacher/subjects',
        [ClassSubjectController::class, 'storeIndependentSubject']
    )->name('teacher.subjects.store');

    // Define this application endpoint.
    Route::post(
        '/teacher/subjects/{classSubject}/students',
        [ClassSubjectController::class, 'syncTeacherSubjectStudents']
    )->name('teacher.subjects.students.sync');

    // Define this application endpoint.
    Route::patch(
        '/teacher/subjects/{classSubject}',
        [ClassSubjectController::class, 'updateTeacherSubject']
    )->name('teacher.subjects.update');
});
