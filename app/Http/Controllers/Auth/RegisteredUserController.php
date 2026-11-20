<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\ActivityLogger;
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
            'account_id' => ['required', 'string', 'max:50'],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)->letters()->numbers(),
            ],
            'student_id' => ['nullable', 'string', 'max:50'],
            'teacher_id' => ['nullable', 'string', 'max:50'],
        ]);

        $idColumn = $request->role === 'teacher' ? 'teacher_id' : 'student_id';

        $accountIdTaken = User::where($idColumn, $validated['account_id'])->exists();

        if ($accountIdTaken) {
            throw ValidationException::withMessages([
                'account_id' => 'This Account ID is already taken.',
            ]);
        }

        $user = User::create([
            'fname' => $validated['fname'],
            'lname' => $validated['lname'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'student_id' => $validated['role'] === 'student' ? $validated['account_id'] : null,
            'teacher_id' => $validated['role'] === 'teacher' ? $validated['account_id'] : null,
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));
        Auth::login($user);

        app(ActivityLogger::class)->log(
            'auth.registered',
            'User registered a new account.',
            $user,
            [
                'role' => $user->role,
                'account_id' => $user->role === 'teacher' ? $user->teacher_id : $user->student_id,
            ]
        );

        return redirect(route('dashboard', absolute: false));
    }
}
