<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [
            DashboardController::class,
            'index',
        ])->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');

        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store');

        Route::patch('/users/{user}', [UserController::class, 'update'])
            ->name('users.update');

        Route::patch('/users/{user}/password', [UserController::class, 'resetPassword'])
            ->name('users.password');
        // Class monitoring and management
        Route::get('/classes', [ClassController::class, 'index'])
            ->name('classes.index');

        Route::post('/classes', [ClassController::class, 'store'])
            ->name('classes.store');

        Route::patch('/classes/{class}', [ClassController::class, 'update'])
            ->name('classes.update');

        Route::delete('/classes/{class}', [ClassController::class, 'destroy'])
            ->name('classes.destroy');

        Route::get('/classes/{class}', [ClassController::class, 'show'])
            ->name('classes.show');

        // Examination monitoring and management
        Route::get('/exams', [ExamController::class, 'index'])
            ->name('exams.index');

        Route::post('/exams', [ExamController::class, 'store'])
            ->name('exams.store');

        Route::patch('/exams/{exam}', [ExamController::class, 'update'])
            ->name('exams.update');

        Route::delete('/exams/{exam}', [ExamController::class, 'destroy'])
            ->name('exams.destroy');

        Route::get('/exams/{exam}', [ExamController::class, 'show'])
            ->name('exams.show');
        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])
            ->name('activity-logs.index');

        // System Settings
        Route::get('/settings', [SystemSettingController::class, 'index'])
            ->name('settings.index');

        Route::patch('/settings', [SystemSettingController::class, 'update'])
            ->name('settings.update');
        Route::get(
            '/activity-logs/export',
            [ActivityLogController::class, 'export']
        )->name('activity-logs.export');

        // Subject monitoring and management
        Route::get('/subjects', [SubjectController::class, 'index'])
            ->name('subjects.index');
        Route::post('/subjects', [SubjectController::class, 'store'])
            ->name('subjects.store');
        Route::patch('/subjects/{subject}', [SubjectController::class, 'update'])
            ->name('subjects.update');
        Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])
            ->name('subjects.destroy');
    });
