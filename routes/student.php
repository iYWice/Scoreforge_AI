<?php

use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\ExamAttemptController;
use Illuminate\Support\Facades\Route;

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
});
