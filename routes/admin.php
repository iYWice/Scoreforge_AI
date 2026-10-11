<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SystemSettingController;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [
            DashboardController::class,
            'index'
        ])->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');

        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store');

        Route::patch('/users/{user}', [UserController::class, 'update'])
            ->name('users.update');

        Route::patch('/users/{user}/password', [UserController::class, 'resetPassword'])
            ->name('users.password');
        // Class monitoring
        Route::get('/classes', [ClassController::class, 'index'])
            ->name('classes.index');

        Route::get('/classes/{class}', [ClassController::class, 'show'])
            ->name('classes.show');

        // Examination monitoring
        Route::get('/exams', [ExamController::class, 'index'])
            ->name('exams.index');

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
    });
