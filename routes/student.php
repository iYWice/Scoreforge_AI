<?php

use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\ExamAttemptController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\ClassController;

Route::middleware(['auth', 'role:student'])->group(function () {

    Route::get(
        '/student/dashboard',
        [DashboardController::class, 'index']
    )->name('student.dashboard');


    // Exam code page
    Route::get(
        '/student/exam',
        [ExamAttemptController::class, 'showCodeForm']
    )->name('student.exam.code');


    // Process exam code
    Route::post(
        '/student/exam/start',
        [ExamAttemptController::class, 'startExam']
    )->name('student.exam.start');


    // Begin exam after instructions
    Route::post(
        '/student/exam/begin',
        [ExamAttemptController::class, 'beginExam']
    )->name('student.exam.begin');


    // Take exam
    Route::get(
        '/student/exam/{attempt}/take',
        [ExamAttemptController::class, 'takeExam']
    )->name('student.exam.take');


    // Submit exam
    Route::post(
        '/student/exam/{attempt}/submit',
        [ExamAttemptController::class, 'submitExam']
    )->name('student.exam.submit');


    // Exam result
    Route::get(
        '/student/exam/{attempt}/result',
        [ExamAttemptController::class, 'result']
    )->name('student.exam.result');

    Route::get(
        '/student/classes',
        [ClassController::class, 'index']
    )->name('student.classes.index');

    Route::post(
        '/student/classes/join',
        [ClassController::class, 'join']
    )->name('student.classes.join');

    Route::get(
        '/student/classes/{class}',
        [ClassController::class, 'show']
    )->name('student.classes.show');resources/views/student/classes/
});
