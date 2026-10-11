@extends('layouts.app')

@section('title', 'Activity Logs | AI-Q')
@section('page-title', 'Activity Logs')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">
            System Activity Logs
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            Review important activities performed inside AI-Q.
        </p>
    </div>

    <form method="GET" action="{{ route('admin.activity-logs.index') }}"
        class="rounded-xl border border-slate-200 bg-white p-4">

        <div class="grid gap-3 md:grid-cols-5">
            <input type="search" name="search"
                value="{{ request('search') }}"
                placeholder="Search activity or user..."
                class="rounded-xl border-slate-300 text-sm md:col-span-2">

            <select name="action" class="rounded-xl border-slate-300 text-sm">
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
                class="rounded-xl border-slate-300 text-sm">

            <input type="date" name="date_to"
                value="{{ request('date_to') }}"
                class="rounded-xl border-slate-300 text-sm">
        </div>

        <div class="mt-3 flex gap-3">
            <button class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white">
                Apply Filters
            </button>

            <a href="{{ route('admin.activity-logs.index') }}"
                class="rounded-xl border border-slate-200 px-5 py-2 text-sm font-medium text-slate-600">
                Clear
            </a>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-4">Date & Time</th>
                    <th class="px-5 py-4">User</th>
                    <th class="px-5 py-4">Action</th>
                    <th class="px-5 py-4">Description</th>
                    <th class="px-5 py-4">IP Address</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                <tr>
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

                    <td class="px-5 py-4">
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">
                            {{ $log->action }}
                        </span>
                    </td>

                    <td class="max-w-sm px-5 py-4 text-slate-600">
                        {{ $log->description }}
                    </td>

                    <td class="px-5 py-4 font-mono text-xs text-slate-500">
                        {{ $log->ip_address ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-slate-500">
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