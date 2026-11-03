@extends('layouts.app')

@section('title', 'Generated Questions')
@section('eyebrow', 'AI Question Builder')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-8">
        <a
            href="{{ route('teacher.ai-questions.create', $exam) }}"
            class="text-sm font-semibold text-slate-500 hover:text-indigo-600">
            ← Back to Question Builder
        </a>

        <h2 class="mt-5 text-2xl font-bold text-slate-950">
            Generated Questions
        </h2>

        <p class="mt-2 text-sm text-slate-500">
            {{ count($session->generated_questions ?? []) }}
            questions from {{ $session->source_name }}.
        </p>

        @if(session('success'))
        <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
        @endif
    </div>

    @if($session->status === 'used')
    <div class="mb-5 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        These questions have already been added to an examination.
    </div>
    @else
    <form
        method="POST"
        action="{{ route('teacher.ai-questions.commit', [$exam, $session]) }}">
        @csrf

        <div class="mb-5 flex flex-col gap-3 rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-indigo-950">
                Select questions to save or add to <strong>{{ $exam->title }}</strong>.
            </p>

            <div class="flex flex-wrap gap-2">
                @if($session->purpose !== 'question_bank')
                <button
                    type="submit"
                    name="action"
                    value="save"
                    class="rounded-xl border border-indigo-200 bg-white px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                    Save selected for later
                </button>
                @endif

                <button
                    type="submit"
                    name="action"
                    value="use"
                    class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Add selected to this exam
                </button>
            </div>
        </div>

        <div class="space-y-4">
            @foreach($session->generated_questions ?? [] as $index => $question)
            <article class="rounded-2xl border border-slate-200 bg-white p-6">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            name="selected_questions[]"
                            value="{{ $index }}"
                            checked
                            class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">

                        <h3 class="font-semibold text-slate-950">
                            {{ $index + 1 }}. {{ $question['question_text'] }}
                        </h3>
                    </div>

                    <span class="shrink-0 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ str_replace('_', ' ', $question['question_type']) }}
                    </span>
                </div>

                <p class="mb-4 text-xs font-semibold uppercase tracking-wide text-slate-400">
                    {{ $question['topic'] ?? 'General' }}
                </p>

                @if(!empty($question['options']))
                <div class="mb-4 space-y-2">
                    @foreach($question['options'] as $option)
                    <div class="rounded-lg bg-slate-50 px-4 py-2 text-sm text-slate-700">
                        {{ $option }}
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="border-t border-slate-100 pt-4 text-sm">
                    <span class="font-semibold text-slate-500">Correct Answer:</span>
                    <span class="ml-1 font-semibold text-emerald-700">
                        {{ $question['correct_answer'] }}
                    </span>
                </div>
            </article>
            @endforeach
        </div>
    </form>
    @endif
</div>
<details class="mt-5 border-t border-slate-100 pt-4">
    <summary class="cursor-pointer text-sm font-semibold text-indigo-600">
        Edit question
    </summary>

    <form
        method="POST"
        action="{{ route('teacher.ai-questions.update-question', [
            'exam' => $exam->id,
            'session' => $session->id,
            'questionIndex' => $index,
        ]) }}"
        class="mt-4 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
                Question
            </label>
            <textarea
                name="question_text"
                required
                maxlength="1000"
                class="w-full rounded-lg border-slate-300">{{ old('question_text', $question['question_text']) }}</textarea>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
                Topic
            </label>
            <input
                name="topic"
                value="{{ old('topic', $question['topic'] ?? '') }}"
                maxlength="255"
                class="w-full rounded-lg border-slate-300">
        </div>

        @if($question['question_type'] === 'multiple_choice')
        @foreach($question['options'] as $optionIndex => $option)
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
                Choice {{ $optionIndex + 1 }}
            </label>
            <input
                name="options[]"
                value="{{ $option }}"
                required
                maxlength="500"
                class="w-full rounded-lg border-slate-300">
        </div>
        @endforeach
        @endif

        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
                Correct answer
            </label>
            <input
                name="correct_answer"
                value="{{ old('correct_answer', $question['correct_answer']) }}"
                required
                maxlength="500"
                class="w-full rounded-lg border-slate-300">
            @if($question['question_type'] === 'multiple_choice')
            <p class="mt-1 text-xs text-slate-500">
                For multiple choice, this must exactly match one of the choices.
            </p>
            @endif
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
                Points
            </label>
            <input
                type="number"
                name="points"
                value="{{ old('points', $question['points'] ?? 1) }}"
                min="1"
                max="100"
                required
                class="w-32 rounded-lg border-slate-300">
        </div>

        <button
            type="submit"
            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
            Save changes
        </button>
    </form>
</details>
@endsection