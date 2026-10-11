<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Services\ActivityLogger;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->where(function ($q) use ($search) {
                    $q->where('fname', 'like', "%{$search}%")
                        ->orWhere('lname', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(
                in_array($request->input('role'), ['admin', 'teacher', 'student'], true),
                fn($query) => $query->where('role', $request->input('role'))
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['teacher', 'student'])],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        app(ActivityLogger::class)->log(
            'user.created',
            'Administrator created a user account.',
            $user,
            ['role' => $user->role]
        );

        return back()->with('success', 'User account created successfully.');
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            return back()->withErrors([
                'user' => 'Administrator accounts cannot be edited here.',
            ]);
        }

        $data = $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['teacher', 'student'])],
        ]);

        // Avoid changing a user's role after they have related records.
        if ($user->role !== $data['role']) {
            $hasRecords = $user->createdClasses()->exists()
                || $user->enrolledClasses()->exists()
                || \App\Models\Exam::where('created_by', $user->id)->exists()
                || \App\Models\ExamAttempt::where('student_id', $user->id)->exists();

            if ($hasRecords) {
                return back()->withErrors([
                    'role' => 'Role cannot be changed while this account has academic records.',
                ]);
            }
        }

        app(ActivityLogger::class)->log(
            'user.updated',
            'Administrator updated a user account.',
            $user
        );

        return back()->with('success', 'User account updated successfully.');
    }

    public function resetPassword(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            return back()->withErrors([
                'user' => 'Administrator passwords cannot be reset here.',
            ]);
        }

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'remember_token' => null,
        ])->save();

        return back()->with('success', 'Password reset successfully.');
    }
}
