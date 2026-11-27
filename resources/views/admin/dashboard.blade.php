@extends('layouts.app')

@section('title', 'Admin Dashboard | AI-Q')
@section('page-title', 'System Administration')

@section('content')

<div class="space-y-6 aiq-admin-dashboard">

    <div class="aiq-panel aiq-panel-interactive p-5 sm:p-6">
        <h1 class="text-2xl font-bold text-slate-900">
            System Overview
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            Monitor and manage the AI-Q examination platform.
        </p>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">

        @foreach ([
        'Total Users' => $stats['users'],
        'Teachers' => $stats['teachers'],
        'Students' => $stats['students'],
        'Classes' => $stats['classes'],
        'Examinations' => $stats['exams'],
        'Exam Attempts' => $stats['attempts'],
        ] as $label => $value)

        <div class="rounded-xl border border-slate-200 bg-white p-5 aiq-panel aiq-panel-interactive">
            <p class="text-sm text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">
                {{ number_format($value) }}
            </p>
        </div>

        @endforeach

    </div>

    <div class="grid gap-6 lg:grid-cols-2">

        <div class="rounded-xl border border-slate-200 bg-white p-5 aiq-panel aiq-panel-interactive">
            <h2 class="mb-4 font-semibold text-slate-900">
                Recent Accounts
            </h2>

            @forelse($recentUsers as $user)
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 py-3 last:border-0 aiq-panel-interactive">
                <div>
                    <p class="font-medium text-slate-900">
                        {{ $user->fname }} {{ $user->lname }}
                    </p>
                    <p class="text-xs text-slate-500">
                        {{ $user->email }}
                    </p>
                </div>
                <span class="text-sm capitalize text-slate-500 aiq-badge">
                    {{ $user->role }}
                </span>
            </div>
            @empty
            <p class="rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-500 aiq-panel">No accounts found.</p>
            @endforelse
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 aiq-panel aiq-panel-interactive">
            <h2 class="mb-4 font-semibold text-slate-900">
                Recent Examinations
            </h2>

            @forelse($recentExams as $exam)
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 py-3 last:border-0 aiq-panel-interactive">
                <div>
                    <p class="font-medium text-slate-900">
                        {{ $exam->title }}
                    </p>
                    <p class="text-xs text-slate-500">
                        {{ $exam->teacher?->fname }}
                        {{ $exam->teacher?->lname }}
                    </p>
                </div>

                <span class="text-sm capitalize text-slate-500 aiq-badge">
                    {{ $exam->status }}
                </span>
            </div>
            @empty
            <p class="rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-500 aiq-panel">No examinations found.</p>
            @endforelse
        </div>

    </div>

</div>

@endsection