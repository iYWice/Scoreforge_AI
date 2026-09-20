<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\ExamAttemptController;
use App\Http\Controllers\Teacher\ClassSubjectController;
use App\Http\Controllers\Teacher\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        'admin' => redirect('/admin/dashboard'),
        'teacher' => redirect('/teacher/dashboard'),
        'student' => redirect('/student/dashboard'),
        default => abort(403),
    };
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name('dashboard');

        Route::get(
            '/class-subjects',
            [ClassSubjectController::class, 'index']
        )->name('class-subjects.index');

        Route::post(
            '/classes',
            [ClassSubjectController::class, 'storeClass']
        )->name('classes.store');

        Route::post(
            '/subjects',
            [ClassSubjectController::class, 'storeSubject']
        )->name('subjects.store');

        Route::delete(
            '/classes/{id}',
            [ClassSubjectController::class, 'destroyClass']
        )->name('classes.destroy');

        Route::delete(
            '/subjects/{id}',
            [ClassSubjectController::class, 'destroySubject']
        )->name('subjects.destroy');
    });

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    });
    Route::middleware(['auth', 'role:student'])->group(function () {

        Route::get('/student/exam', [ExamAttemptController::class, 'enterCode']);
        Route::post('/student/exam/start', [ExamAttemptController::class, 'startExam']);
    });
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });
});
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/teacher.php';
require __DIR__ . '/student.php';
