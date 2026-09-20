@extends('layouts.app')

@section('title', 'Edit Question')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    {{-- Back Link & Header --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.questions.index', $question->exam_id ?? '') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Question Builder
        </a>
        <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold uppercase tracking-wider">
            {{ strtoupper(str_replace('_', ' ', $question->question_type)) }}
        </span>
    </div>
    @if($errors->any())
    <div class="bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 rounded-2xl p-4">

        <div class="flex items-start gap-3">

            <svg
                class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>

            <div>
                <h3 class="text-xs font-bold text-rose-800 dark:text-rose-300 uppercase tracking-wider">
                    Please fix the following errors:
                </h3>

                <ul class="mt-2 list-disc list-inside text-xs text-rose-700 dark:text-rose-400 space-y-1">

                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach

                </ul>
            </div>

        </div>

    </div>
    @endif

    {{-- Alert Banner --}}
    @if(session('success'))
    <div class="bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-4 transition-colors">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-xs font-semibold text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    {{-- Main Edit Form Card --}}
    <div class="bg-white dark:bg-slate-900 shadow-xl shadow-slate-900/5 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 transition-colors">

        <div class="mb-6 pb-6 border-b border-slate-100 dark:border-slate-800">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                Edit Question
            </h1>
            <p class="text-xs font-medium text-slate-400 dark:text-slate-500 mt-1">
                Update prompt wording, options, or scoring allocation.
            </p>
        </div>

        <form method="POST" action="{{ route('teacher.questions.update', $question->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Question Text --}}
            <div class="space-y-1.5">
                <label for="question_text" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Question Text
                </label>
                <textarea
                    id="question_text"
                    name="question_text"
                    rows="3"
                    required
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all placeholder:text-slate-400">{{ old('question_text', $question->question_text) }}</textarea>
            </div>
            {{-- Topic --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                    Topic
                </label>

                <input
                    type="text"
                    name="topic"
                    value="{{ old('topic', $question->topic) }}"
                    placeholder="Example: SQL Joins, Normalization, Arrays"
                    class="w-full rounded-xl border-slate-300 dark:border-slate-700
               dark:bg-slate-900 dark:text-white">

                @error('topic')
                <p class="mt-1 text-sm text-red-500">
                    {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Multiple Choice Fields --}}
            @if($question->question_type === 'mcq')
            @php
            $choices = $question->options
            ->pluck('option_text')
            ->values();
            @endphp

            <div class="space-y-3 pt-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Answer Choices
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 mb-1 uppercase">Choice A</span>
                        <input type="text" name="choice_a" value="{{ old('choice_a', $choices[0] ?? '') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all">
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 mb-1 uppercase">Choice B</span>
                        <input type="text" name="choice_b" value="{{ old('choice_b', $choices[1] ?? '') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all">
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 mb-1 uppercase">Choice C</span>
                        <input type="text" name="choice_c" value="{{ old('choice_c', $choices[2] ?? '') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all">
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 mb-1 uppercase">Choice D</span>
                        <input type="text" name="choice_d" value="{{ old('choice_d', $choices[3] ?? '') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all">
                    </div>
                </div>
            </div>
            @endif

            {{-- Correct Answer Section --}}
            @if($question->question_type === 'tf')
            <div class="space-y-1.5 pt-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Correct Answer Key
                </label>
                <div class="grid grid-cols-2 gap-3 max-w-xs">
                    <label class="flex items-center justify-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 cursor-pointer hover:border-indigo-500 transition-all">
                        <input
                            type="radio"
                            name="correct_answer"
                            value="True"
                            {{ old('correct_answer', $question->correct_answer) == 'True' ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800">
                        <span class="ml-2 text-xs font-bold text-slate-700 dark:text-slate-300">True</span>
                    </label>
                    <label class="flex items-center justify-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 cursor-pointer hover:border-indigo-500 transition-all">
                        <input
                            type="radio"
                            name="correct_answer"
                            value="False"
                            {{ old('correct_answer', $question->correct_answer) == 'False' ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800">
                        <span class="ml-2 text-xs font-bold text-slate-700 dark:text-slate-300">False</span>
                    </label>
                </div>
            </div>
            @else
            <div class="space-y-1.5 pt-2">
                <label for="correct_answer" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Correct Answer Key
                </label>
                <input
                    type="text"
                    id="correct_answer"
                    name="correct_answer"
                    value="{{ old('correct_answer', $question->correct_answer) }}"
                    required
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all">
            </div>
            @endif

            {{-- Points Assignment --}}
            <div class="space-y-1.5 pt-2">
                <label for="points" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Points
                </label>
                <input
                    type="number"
                    id="points"
                    name="points"
                    min="1"
                    value="{{ old('points', $question->points) }}"
                    required
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all">
            </div>

            {{-- Form Actions --}}
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-semibold text-xs px-6 py-3 rounded-xl shadow-lg shadow-indigo-500/20 transition-all duration-200 hover:scale-[1.01] active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Save Changes</span>
                </button>
            </div>

        </form>

    </div>

</div>

@endsection