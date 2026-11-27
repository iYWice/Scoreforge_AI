<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Apply the shared middleware, prefix, and name scope to this route group.
Route::middleware('guest')->group(function () {
    // Define this application endpoint.
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    // Define this application endpoint.
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Define this application endpoint.
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    // Define this application endpoint.
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Define this application endpoint.
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    // Define this application endpoint.
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Define this application endpoint.
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    // Define this application endpoint.
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');

    // Define this application endpoint.
    Route::post('check-account-id', function (Request $request) {
        $value = trim((string) $request->input('account_id'));
        $role = $request->input('role');

        if ($value === '') {
            return response()->json(['available' => false, 'message' => 'Enter an Account ID first.']);
        }

        if (! in_array($role, ['student', 'teacher'], true)) {
            return response()->json(['available' => false, 'message' => 'Select your account role first.']);
        }

        if (! preg_match('/^[0-9]+$/', $value)) {
            return response()->json(['available' => false, 'message' => 'Account ID must contain numbers only.']);
        }

        $column = $role === 'student' ? 'student_id' : 'teacher_id';
        $taken = User::where($column, $value)->exists();

        return response()->json([
            'available' => ! $taken,
            'message' => $taken ? 'This Account ID is already taken.' : 'This Account ID is available.',
        ]);
    })->name('register.check.account-id');
});

// Apply the shared middleware, prefix, and name scope to this route group.
Route::middleware('auth')->group(function () {
    // Define this application endpoint.
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    // Define this application endpoint.
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Define this application endpoint.
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Define this application endpoint.
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    // Define this application endpoint.
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Define this application endpoint.
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Define this application endpoint.
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
