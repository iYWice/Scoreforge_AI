@extends('layouts.app')

@section('title', 'Activity Logs | AI-Q')
@section('page-title', 'Activity Logs')

@section('content')
<div class="space-y-6 aiq-activity-logs">
    <header class="flex flex-wrap items-center justify-between gap-4 aiq-panel aiq-panel-interactive p-5 sm:p-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">
            System Activity Logs
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            Review important activities performed inside AI-Q.
        </p>
    </div>
    <a
        href="{{ route('admin.activity-logs.export', request()->query()) }}"
        class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white aiq-button aiq-button-primary">
        Export CSV
    </a>
    </header>

    <form method="GET" action="{{ route('admin.activity-logs.index') }}"
        class="rounded-xl border border-slate-200 bg-white p-4 aiq-panel">

        <div class="grid min-w-0 gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <input type="search" name="search"
                value="{{ request('search') }}"
                placeholder="Search activity or user..."
                class="w-full min-w-0 rounded-xl border-slate-300 text-sm sm:col-span-2 lg:col-span-2 aiq-field">

            <select name="action" class="w-full rounded-xl border-slate-300 text-sm aiq-field">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                <option value="{{ $action }}"
                    @selected(request('action')===$action)>
                    {{ str_replace('.', ' / ', $action) }}
                </option>
                @endforeach
            </select>

            <input type="date" name="date_from"
                value="{{ request('date_from') }}"
                class="w-full rounded-xl border-slate-300 text-sm aiq-field">

            <input type="date" name="date_to"
                value="{{ request('date_to') }}"
                class="w-full rounded-xl border-slate-300 text-sm aiq-field">
        </div>

        <div class="mt-3 flex flex-wrap gap-3">
            <button class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white aiq-button aiq-button-primary">
                Apply Filters
            </button>

            <a href="{{ route('admin.activity-logs.index') }}"
                class="rounded-xl border border-slate-200 px-5 py-2 text-sm font-medium text-slate-600 aiq-button aiq-button-secondary">
                Clear
            </a>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white aiq-panel">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-4">Log ID</th>
                    <th class="px-5 py-4">Date & Time</th>
                    <th class="px-5 py-4">User</th>
                    <th class="px-5 py-4">Account ID</th>
                    <th class="px-5 py-4">Action</th>
                    <th class="px-5 py-4">Description</th>
                    <th class="px-5 py-4">Details</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                <tr>
                    <td class="px-5 py-4 font-mono text-xs text-slate-600">
                        #{{ $log->id }}
                    </td>

                    <td class="whitespace-nowrap px-5 py-4 text-slate-500">
                        {{ $log->created_at?->format('M d, Y h:i A') }}
                    </td>

                    <td class="px-5 py-4">
                        <p class="font-medium text-slate-900">
                            {{ $log->user
                                    ? trim($log->user->fname . ' ' . $log->user->lname)
                                    : 'System / Deleted User' }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ $log->user?->email ?? '—' }}
                        </p>
                    </td>

                    <td class="whitespace-nowrap px-5 py-4 text-slate-500">
                        {{ $log->user
                            ? ($log->user->role === 'teacher'
                                ? ($log->user->teacher_id ?? '—')
                                : ($log->user->student_id ?? '—'))
                            : '—' }}
                    </td>

                    <td class="px-5 py-4">
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 aiq-badge">
                            {{ $log->action }}
                        </span>
                    </td>

                    <td class="max-w-sm px-5 py-4 text-slate-600">
                        {{ $log->description }}
                    </td>

                    <td class="max-w-xs px-5 py-4 text-xs text-slate-500">
                        @if(!empty($log->properties))
                            @foreach($log->properties as $key => $value)
                                <div>
                                    <span class="font-semibold text-slate-700">{{ str_replace('_', ' ', ucwords($key, '_')) }}:</span>
                                    {{ is_bool($value) ? ($value ? 'Yes' : 'No') : ($value ?? '—') }}
                                </div>
                            @endforeach
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-slate-500 aiq-panel">
                        No activity logs found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $logs->links() }}

</div>
@endsection