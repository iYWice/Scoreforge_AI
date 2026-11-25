@extends('layouts.app')

@section('title', 'Teacher Feedback')
@section('eyebrow', 'My Feedback')

@section('content')
<div class="mx-auto max-w-4xl space-y-6 aiq-teacher-feedback">
    <header class="aiq-panel aiq-panel-interactive p-5 sm:p-6">
        <h1 class="text-2xl font-extrabold text-slate-950">Teacher Feedback</h1>
        <p class="mt-1 text-sm text-slate-500">
            Reviews your teacher has sent about your exams and overall progress.
        </p>
    </header>

    <div class="space-y-4">
        @forelse($analyses as $analysis)
            <article class="rounded-xl border border-slate-200 bg-white p-5 aiq-panel aiq-panel-interactive">
                <p class="text-xs font-bold uppercase tracking-wide text-indigo-600 aiq-badge">
                    @if($analysis->scope === 'overall')
                        Overall performance
                    @else
                        {{ $analysis->attempt?->exam?->title ?? 'Exam review' }}
                        @if($analysis->attempt?->exam?->subject)
                            · {{ $analysis->attempt->exam->subject->name }}
                        @endif
                    @endif
                </p>

                <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">
                    {{ $analysis->content }}
                </p>

                <p class="mt-3 text-xs text-slate-400">
                    Sent {{ $analysis->sent_at?->format('M d, Y g:i A') }}
                </p>

                @if($analysis->attempt)
                    <a href="{{ route('student.exam.result', $analysis->attempt) }}"
                       class="mt-3 inline-flex items-center rounded-xl text-sm font-semibold text-indigo-600 hover:text-indigo-800 aiq-button aiq-button-secondary">
                        Open exam result →
                    </a>
                @endif
            </article>
        @empty
            <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 aiq-panel">
                Your teacher hasn’t sent feedback yet.
            </p>
        @endforelse
    </div>

    {{ $analyses->links() }}
</div>
@endsection