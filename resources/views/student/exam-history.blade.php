@extends('layouts.app')

@section('title', 'Exam History')
@section('eyebrow', 'Examination History')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-950">
                Your exam history
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Review your completed exams and results.
            </p>
        </div>

        <a
            href="{{ route('student.dashboard') }}"
            class="text-sm font-bold text-indigo-600 hover:text-indigo-800">
            ← Back to dashboard
        </a>
    </div>

    <div class="border-t border-slate-200">
        @forelse($attempts as $attempt)
            @php
                $passingScore = $attempt->exam?->passing_score ?? 0;
                $passed = ($attempt->score ?? 0) >= $passingScore;
                $percentage = ($attempt->total_score ?? 0) > 0
                    ? ($attempt->score / $attempt->total_score) * 100
                    : 0;
            @endphp

            <article class="grid gap-4 border-b border-slate-200 py-5
                            sm:grid-cols-[minmax(0,1fr)_auto_auto]
                            sm:items-center">
                <div>
                    <h2 class="font-bold text-slate-900">
                        {{ $attempt->exam?->title ?? 'Exam unavailable' }}
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        {{ $attempt->exam?->subject?->name ?? 'No subject' }}
                        ·
                        {{ $attempt->submitted_at?->format('M d, Y g:i A') ?? 'Date unavailable' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-bold text-slate-600">
                        {{ $attempt->score ?? 0 }} / {{ $attempt->total_score ?? 0 }}
                        ({{ number_format($percentage, 1) }}%)
                    </p>
                    <span class="text-xs font-bold {{ $passed ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $passed ? 'Passed' : 'Needs improvement' }}
                    </span>
                </div>

                <a
                    href="{{ route('student.exam.result', $attempt) }}"
                    class="text-sm font-bold text-indigo-600 hover:text-indigo-800">
                    View result →
                </a>
            </article>
        @empty
            <div class="py-12 text-center">
                <p class="font-bold text-slate-700">No completed exams yet.</p>
                <p class="mt-1 text-sm text-slate-500">
                    Completed exams will appear here.
                </p>
            </div>
        @endforelse
    </div>

    <div>
        {{ $attempts->links() }}
    </div>
</div>
@endsection