<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\ExamAttemptController;
use App\Http\Controllers\Teacher\ClassSubjectController;
use App\Http\Controllers\Teacher\DashboardController;
use Illuminate\Support\Facades\Route;

// Define this application endpoint.
Route::get('/', function () {
    return view('welcome');
});

// Define this application endpoint.
Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        'admin' => redirect('/admin/dashboard'),
        'teacher' => redirect('/teacher/dashboard'),
        'student' => redirect('/student/dashboard'),
        default => abort(403),
    };
})->middleware(['auth'])->name('dashboard');

// Apply the shared middleware, prefix, and name scope to this route group.
Route::middleware('auth')->group(function () {
    // Define this application endpoint.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Define this application endpoint.
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Define this application endpoint.
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Apply the shared middleware, prefix, and name scope to this route group.
Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    // Register the routes that belong to this shared route group.
    ->group(function () {

        // Define this application endpoint.
        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name('dashboard');

        // Define this application endpoint.
        Route::get(
            '/class-subjects',
            [ClassSubjectController::class, 'index']
        )->name('class-subjects.index');

        // Define this application endpoint.
        Route::post(
            '/classes',
            [ClassSubjectController::class, 'storeClass']
        )->name('classes.store');

        // Define this application endpoint.
        Route::post(
            '/subjects',
            [ClassSubjectController::class, 'storeSubject']
        )->name('subjects.store');

        // Define this application endpoint.
        Route::delete(
            '/classes/{id}',
            [ClassSubjectController::class, 'destroyClass']
        )->name('classes.destroy');

        // Define this application endpoint.
        Route::delete(
            '/subjects/{id}',
            [ClassSubjectController::class, 'destroySubject']
        )->name('subjects.destroy');
    });

// Apply the shared middleware, prefix, and name scope to this route group.
Route::middleware(['auth', 'role:student'])->group(function () {
    // Define this application endpoint.
    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    });
    // Apply the shared middleware, prefix, and name scope to this route group.
    Route::middleware(['auth', 'role:student'])->group(function () {

        // Define this application endpoint.
        Route::get('/student/exam', [ExamAttemptController::class, 'enterCode']);
        // Define this application endpoint.
        Route::post('/student/exam/start', [ExamAttemptController::class, 'startExam']);
    });
});

// Apply the shared middleware, prefix, and name scope to this route group.
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Define this application endpoint.
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });
});
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/teacher.php';
require __DIR__.'/student.php';
