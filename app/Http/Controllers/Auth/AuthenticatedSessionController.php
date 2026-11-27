<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Represents the AuthenticatedSessionController application component.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    /**
     * Handles create.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    /**
     * Handles store.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        app(ActivityLogger::class)->log(
            'auth.login',
            'User logged in.',
            $user
        );

        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        if ($user->role === 'teacher') {
            return redirect('/teacher/dashboard');
        }

        return redirect('/student/dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    /**
     * Handles destroy.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            app(ActivityLogger::class)->log(
                'auth.logout',
                'User logged out.',
                $user
            );
        }
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
