@extends('layouts.app')

@section('title', 'Exam Details | AI-Q')
@section('page-title', 'Exam Details')

@section('content')
<div class="space-y-6">

    <a href="{{ route('admin.exams.index') }}"
        class="text-sm font-medium text-indigo-600">
        ← Back to Exams
    </a>

    <div class="rounded-xl border border-slate-200 bg-white p-6">
        <h1 class="text-2xl font-bold">{{ $exam->title }}</h1>

        <div class="mt-5 grid gap-4 text-sm sm:grid-cols-3">
            <div>
                <p class="text-slate-500">Exam Code</p>
                <p class="font-semibold">{{ $exam->exam_code }}</p>
            </div>
            <div>
                <p class="text-slate-500">Status</p>
                <p class="font-semibold capitalize">{{ $exam->status }}</p>
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

    <div class="rounded-xl border border-slate-200 bg-white p-6">
        <h2 class="mb-4 text-lg font-bold">Questions</h2>

        @forelse($questions as $index => $question)
        <div class="border-b border-slate-100 py-4 last:border-0">
            <div class="flex items-start justify-between gap-4">
                <p class="font-medium">
                    {{ $index + 1 }}. {{ $question->question_text }}
                </p>
                <span class="shrink-0 text-xs capitalize text-slate-500">
                    {{ str_replace('_', ' ', $question->question_type) }}
                </span>
            </div>

            @if($question->options->isNotEmpty())
            <div class="mt-3 space-y-1 pl-5 text-sm text-slate-600">
                @foreach($question->options as $option)
                <p>{{ $option->option_text }}</p>
                @endforeach
            </div>
            @endif

            <p class="mt-3 text-sm text-emerald-700">
                Correct Answer: {{ $question->correct_answer }}
            </p>
        </div>
        @empty
        <p class="text-sm text-slate-500">
            No questions added to this exam.
        </p>
        @endforelse
    </div>

</div>
@endsection