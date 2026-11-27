{-- Use the application layout and configure inherited page structure. --}
@extends('layouts.app')

{-- Define this page section and its presentation content. --}
@section('title', 'Exam Details | AI-Q')
{-- Define this page section and its presentation content. --}
@section('page-title', 'Exam Details')

{-- Define this page section and its presentation content. --}
@section('content')
<div class="space-y-6 aiq-exam-details-admin">

    <a href="{{ route('admin.exams.index') }}"
        class="text-sm font-medium text-indigo-600 aiq-button aiq-button-secondary">
        ← Back to Exams
    </a>

    <div class="rounded-xl border border-slate-200 bg-white p-6 aiq-panel aiq-panel-interactive">
        <h1 class="text-2xl font-bold">{{ $exam->title }}</h1>

        <div class="mt-5 grid gap-4 text-sm sm:grid-cols-3">
            <div>
                <p class="text-slate-500">Exam Code</p>
                <p class="mt-1 w-fit font-mono font-semibold aiq-badge">{{ $exam->exam_code }}</p>
            </div>
            <div>
                <p class="text-slate-500">Status</p>
                <p class="mt-1 w-fit font-semibold capitalize aiq-badge">{{ $exam->status }}</p>
            </div>
            <div>
                <p class="text-slate-500">Subject</p>
                <p class="font-semibold">
                    {{ $exam->subject?->name ?? 'Unassigned' }}
                </p>
            </div>
            <div>
                <p class="text-slate-500">Teacher</p>
                <p class="font-semibold">
                    {{ $exam->teacher?->fname }}
                    {{ $exam->teacher?->lname }}
                </p>
            </div>
            <div>
                <p class="text-slate-500">Questions</p>
                <p class="font-semibold">{{ $exam->questions_count }}</p>
            </div>
            <div>
                <p class="text-slate-500">Attempts</p>
                <p class="font-semibold">{{ $exam->attempts_count }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 aiq-panel aiq-panel-interactive">
        <h2 class="mb-4 text-lg font-bold">Questions</h2>

        {-- Iterate over the related records and render each result. --}
        @forelse($questions as $index => $question)
        <div class="border-b border-slate-100 py-4 last:border-0 aiq-panel-interactive">
            <div class="flex items-start justify-between gap-4">
                <p class="font-medium">
                    {{ $index + 1 }}. {{ $question->question_text }}
                </p>
                <span class="shrink-0 text-xs capitalize text-slate-500 aiq-badge">
                    {{ str_replace('_', ' ', $question->question_type) }}
                </span>
            </div>

            {-- Render this content conditionally based on the current view state. --}
            @if($question->options->isNotEmpty())
            <div class="mt-3 space-y-1 pl-5 text-sm text-slate-600">
                {-- Iterate over the related records and render each result. --}
                @foreach($question->options as $option)
                <p>{{ $option->option_text }}</p>
                @endforeach
            </div>
            @endif

            <p class="mt-3 text-sm text-emerald-700 aiq-alert aiq-alert-success">
                Correct Answer: {{ $question->correct_answer }}
            </p>
        </div>
        @empty
        <p class="rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-500 aiq-panel">
            No questions added to this exam.
        </p>
        @endforelse
    </div>

</div>
@endsection