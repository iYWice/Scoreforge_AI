<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        abort_if(
            SystemSetting::getValue('allow_registration', '1') !== '1',
            403,
            'Public registration is currently disabled.'
        );

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        abort_if(
            SystemSetting::getValue('allow_registration', '1') !== '1',
            403,
            'Public registration is currently disabled.'
        );

        $validated = $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:teacher,student'],
            'student_id' => [
                'exclude_unless:role,student',
                'required',
                'string',
                'max:50',
                'unique:users,student_id',
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)->letters()->numbers(),
            ],
        ]);

        $user = User::create([
            'fname' => $validated['fname'],
            'lname' => $validated['lname'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'student_id' => $validated['student_id'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
