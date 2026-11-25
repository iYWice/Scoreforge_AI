@extends('layouts.app')

@section('title', 'User Management | AI-Q')
@section('page-title', 'User Management')

@section('content')
<div class="space-y-6 aiq-user-management" x-data="{ createOpen: false, editUser: null, passwordUser: null }">

    <div class="flex flex-wrap items-center justify-between gap-4 aiq-panel aiq-panel-interactive p-5 sm:p-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">User Management</h1>
            <p class="mt-1 text-sm text-slate-500">
                Manage teacher and student accounts.
            </p>
        </div>

        <button type="button" @click="createOpen = true"
            class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 aiq-button aiq-button-primary">
            + Create User
        </button>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700 aiq-alert aiq-alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700 aiq-alert aiq-alert-error">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="GET" action="{{ route('admin.users.index') }}"
        class="grid min-w-0 gap-3 sm:grid-cols-[minmax(0,1fr)_190px_auto] sm:items-center aiq-panel p-4">
        <input type="search" name="search" value="{{ request('search') }}"
            placeholder="Search name or email..."
            class="w-full min-w-0 rounded-xl border-slate-300 text-sm aiq-field">

        <select name="role" class="w-full rounded-xl border-slate-300 text-sm aiq-field">
            <option value="">All Roles</option>
            @foreach(['admin', 'teacher', 'student'] as $role)
            <option value="{{ $role }}" @selected(request('role')===$role)>
                {{ ucfirst($role) }}
            </option>
            @endforeach
        </select>

        <button class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-medium text-white aiq-button aiq-button-secondary">
            Filter
        </button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white aiq-panel">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-4">Name</th>
                    <th class="px-5 py-4">Account ID</th>
                    <th class="px-5 py-4">Email</th>
                    <th class="px-5 py-4">Role</th>
                    <th class="px-5 py-4">Registered</th>
                    <th class="px-5 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                <tr>
                    <td class="px-5 py-4 font-medium text-slate-900">
                        {{ $user->fname }} {{ $user->lname }}
                    </td>
                    <td class="px-5 py-4 font-mono text-xs text-slate-600">
                        {{ $user->role === 'teacher' ? ($user->teacher_id ?? '—') : ($user->student_id ?? '—') }}
                    </td>
                    <td class="px-5 py-4 text-slate-600">{{ $user->email }}</td>
                    <td class="px-5 py-4">
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium capitalize text-indigo-700 aiq-badge">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-slate-500">
                        {{ $user->created_at?->format('M d, Y') }}
                    </td>
                    <td class="px-5 py-4 text-right">
                        @if($user->role !== 'admin')
                        <div class="flex flex-wrap justify-end gap-2">
                            <button type="button"
                                @click="editUser = {{ \Illuminate\Support\Js::from([
                                            'id' => $user->id,
                                            'fname' => $user->fname,
                                            'lname' => $user->lname,
                                            'email' => $user->email,
                                            'role' => $user->role,
                                            'account_id' => $user->role === 'teacher' ? $user->teacher_id : $user->student_id,
                                        ]) }}"
                                class="font-medium text-indigo-600 hover:underline aiq-button aiq-button-secondary">
                                Edit
                            </button>
                            <button type="button"
                                @click="passwordUser = {{ \Illuminate\Support\Js::from([
                                            'id' => $user->id,
                                            'email' => $user->email,
                                        ]) }}"
                                class="text-slate-600 hover:underline aiq-button aiq-button-secondary">
                                Reset Password
                            </button>
                        </div>
                        @else
                        <span class="text-xs text-slate-400">Protected</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                        No accounts found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}

    {{-- Create user --}}
    <div x-show="createOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-950/50 p-4">
        <div class="my-auto w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl aiq-panel">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-lg font-bold">Create User</h2>
                <button type="button" @click="createOpen = false" class="aiq-button aiq-button-secondary" style="width:40px;min-width:40px;height:40px;min-height:40px;padding:0">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <input name="fname" required placeholder="First name"
                        class="w-full rounded-xl border-slate-300 aiq-field">
                    <input name="lname" required placeholder="Last name"
                        class="w-full rounded-xl border-slate-300 aiq-field">
                </div>

                <input name="email" type="email" required placeholder="Email address"
                    class="w-full rounded-xl border-slate-300 aiq-field">

                <select name="role" required class="w-full rounded-xl border-slate-300 aiq-field">
                    <option value="admin">Admin</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                </select>

                <input name="account_id" maxlength="50"
                    placeholder="Account ID (required for teacher/student)"
                    class="w-full rounded-xl border-slate-300 aiq-field">

                <input name="password" type="password" required
                    minlength="8" placeholder="Password"
                    class="w-full rounded-xl border-slate-300 aiq-field">

                <input name="password_confirmation" type="password" required
                    minlength="8" placeholder="Confirm password"
                    class="w-full rounded-xl border-slate-300 aiq-field">

                <button class="w-full rounded-xl bg-indigo-600 py-3 font-semibold text-white aiq-button aiq-button-primary">
                    Create Account
                </button>
            </form>
        </div>
    </div>

    {{-- Edit user --}}
    <div x-show="editUser" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-950/50 p-4">
        <div class="my-auto w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl aiq-panel">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-lg font-bold">Edit User</h2>
                <button type="button" @click="editUser = null" class="aiq-button aiq-button-secondary" style="width:40px;min-width:40px;height:40px;min-height:40px;padding:0">✕</button>
            </div>

            <template x-if="editUser">
                <form method="POST"
                    :action="'{{ url('/admin/users') }}/' + editUser.id"
                    class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <input name="fname" x-model="editUser.fname" required
                        class="w-full rounded-xl border-slate-300 aiq-field">
                    <input name="lname" x-model="editUser.lname" required
                        class="w-full rounded-xl border-slate-300 aiq-field">
                    <input name="email" type="email" x-model="editUser.email" required
                        class="w-full rounded-xl border-slate-300 aiq-field">

                    <select name="role" x-model="editUser.role"
                        class="w-full rounded-xl border-slate-300 aiq-field">
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                    </select>

                    <input name="account_id" x-model="editUser.account_id" required maxlength="50"
                        placeholder="Teacher or Student Account ID"
                        class="w-full rounded-xl border-slate-300 aiq-field">

                    <button class="w-full rounded-xl bg-indigo-600 py-3 font-semibold text-white aiq-button aiq-button-primary">
                        Save Changes
                    </button>
                </form>
            </template>
        </div>
    </div>

    {{-- Reset password --}}
    <div x-show="passwordUser" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-950/50 p-4">
        <div class="my-auto w-full max-w-md rounded-2xl bg-white p-6 shadow-xl aiq-panel">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-lg font-bold">Reset Password</h2>
                <button type="button" @click="passwordUser = null" class="aiq-button aiq-button-secondary" style="width:40px;min-width:40px;height:40px;min-height:40px;padding:0">✕</button>
            </div>

            <template x-if="passwordUser">
                <form method="POST"
                    :action="'{{ url('/admin/users') }}/' + passwordUser.id + '/password'"
                    class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <p class="text-sm text-slate-500" x-text="passwordUser.email"></p>

                    <input name="password" type="password" minlength="8" required
                        placeholder="New password"
                        class="w-full rounded-xl border-slate-300 aiq-field">

                    <input name="password_confirmation" type="password" minlength="8" required
                        placeholder="Confirm new password"
                        class="w-full rounded-xl border-slate-300 aiq-field">

                    <button class="w-full rounded-xl bg-indigo-600 py-3 font-semibold text-white aiq-button aiq-button-primary">
                        Update Password
                    </button>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection