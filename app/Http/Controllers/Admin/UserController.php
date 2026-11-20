<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('student_id', 'like', "%{$search}%")
                        ->orWhere('teacher_id', 'like', "%{$search}%");
                });
            })
            ->when(
                in_array($request->input('role'), ['admin', 'teacher', 'student'], true),
                fn ($query) => $query->where('role', $request->input('role'))
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
            'role' => ['required', Rule::in(['admin', 'teacher', 'student'])],
            'account_id' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if ($data['role'] === 'admin') {
            $data['student_id'] = null;
            $data['teacher_id'] = null;
            unset($data['account_id']);
        } else {
            if (! filled($data['account_id'] ?? null)) {
                return back()->withErrors([
                    'account_id' => 'An Account ID is required for teacher and student accounts.',
                ])->withInput();
            }

            $idColumn = $data['role'] === 'teacher' ? 'teacher_id' : 'student_id';

            $accountIdTaken = User::where($idColumn, $data['account_id'])->exists();

            if ($accountIdTaken) {
                return back()->withErrors([
                    'account_id' => 'This Account ID is already taken.',
                ])->withInput();
            }

            $data[$idColumn] = $data['account_id'];
            unset($data['account_id']);
        }

        $data['password'] = Hash::make($data['password']);
        $data['name'] = trim($data['fname'].' '.$data['lname']);

        $user = User::create($data);

        app(ActivityLogger::class)->log(
            'user.created',
            'Administrator created a user account.',
            $user,
            [
                'role' => $user->role,
                'account_id' => $user->role === 'admin'
                    ? null
                    : ($user->role === 'teacher' ? $user->teacher_id : $user->student_id),
            ]
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
            'account_id' => ['required', 'string', 'max:50'],
        ]);

        // Avoid changing a user's role after they have related records.
        if ($user->role !== $data['role']) {
            $hasRecords = $user->createdClasses()->exists()
                || $user->enrolledClasses()->exists()
                || Exam::where('created_by', $user->id)->exists()
                || ExamAttempt::where('student_id', $user->id)->exists();

            if ($hasRecords) {
                return back()->withErrors([
                    'role' => 'Role cannot be changed while this account has academic records.',
                ]);
            }
        }

        $idColumn = $data['role'] === 'teacher' ? 'teacher_id' : 'student_id';

        $accountIdTaken = User::where($idColumn, $data['account_id'])
            ->where('id', '!=', $user->id)
            ->exists();

        if ($accountIdTaken) {
            return back()->withErrors([
                'account_id' => 'This Account ID is already taken.',
            ])->withInput();
        }

        $data[$idColumn] = $data['account_id'];
        unset($data['account_id']);

        if ($data['role'] === 'teacher') {
            $data['student_id'] = null;
        } else {
            $data['teacher_id'] = null;
        }

        $changedFields = array_keys(
            array_diff_assoc($data, $user->only(array_keys($data)))
        );

        $data['name'] = trim($data['fname'].' '.$data['lname']);

        $user->update($data);

        app(ActivityLogger::class)->log(
            'user.updated',
            'Administrator updated a user account.',
            $user,
            ['changed_fields' => $changedFields]
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

        app(ActivityLogger::class)->log(
            'user.password_reset',
            'Administrator reset a user password.',
            $user
        );

        return back()->with('success', 'Password reset successfully.');
    }
}
