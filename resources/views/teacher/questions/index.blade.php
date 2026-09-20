@extends('layouts.app')

@section('title', 'Question Bank')
@section('eyebrow', 'Exam Workspace')

@section('page-actions')
<div class="flex items-center gap-2">

    <a
        href="{{ route('teacher.exams.show', $exam->id) }}"
        class="hidden sm:inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white
               px-3.5 py-2.5 text-xs font-bold text-slate-600 transition-all duration-200
               hover:-translate-y-0.5 hover:border-slate-300 hover:text-slate-950 hover:shadow-sm
               active:translate-y-0 active:scale-95">
        ← Overview
    </a>

    <button
        type="button"
        onclick="openQuestionBuilder()"
        class="group inline-flex items-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5
               text-xs font-bold text-white transition-all duration-200
               hover:-translate-y-0.5 hover:bg-indigo-600 hover:shadow-lg hover:shadow-indigo-200
               active:translate-y-0 active:scale-95">

        <svg
            class="h-4 w-4 transition-transform duration-300 group-hover:rotate-90"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2.3"
                d="M12 4v16m8-8H4" />
        </svg>

        Add Question
    </button>

</div>
@endsection


@section('content')

@php
$mcqCount = collect($questions)->where('question_type', 'mcq')->count();
$tfCount = collect($questions)->where('question_type', 'tf')->count();
$identificationCount = collect($questions)->where('question_type', 'identification')->count();

$examStatus = strtolower($exam->status ?? 'draft');
@endphp


<div
    x-data="{
        filter: 'all',
        search: '',
        openQuestion: null,
        copied: false,

        copyCode() {
            navigator.clipboard.writeText('{{ $exam->exam_code }}');
            this.copied = true;

            setTimeout(() => {
                this.copied = false;
            }, 1500);
        }
    }"
    class="space-y-8">


    {{-- ====================================================== --}}
    {{-- EXAM IDENTITY                                          --}}
    {{-- ====================================================== --}}

    <section class="border-b border-slate-200 pb-7">

        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">

            <div>

                <div class="flex flex-wrap items-center gap-2">

                    @if($examStatus === 'published')

                    <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold
                                     uppercase tracking-[0.16em] text-emerald-700">

                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Published

                    </span>

                    @elseif($examStatus === 'draft')

                    <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold
                                     uppercase tracking-[0.16em] text-amber-700">

                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                        Draft

                    </span>

                    @else

                    <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold
                                     uppercase tracking-[0.16em] text-slate-500">

                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                        {{ ucfirst($examStatus) }}

                    </span>

                    @endif


                    @if($exam->subject)

                    <span class="text-slate-300">/</span>

                    <span class="text-xs font-semibold text-slate-500">
                        {{ $exam->subject->name }}
                    </span>

                    @endif


                    @if($exam->class)

                    <span class="text-slate-300">/</span>

                    <span class="text-xs font-semibold text-slate-500">
                        {{ $exam->class->name }}
                    </span>

                    @endif

                </div>


                <h2 class="mt-3 text-2xl font-black tracking-[-0.045em] text-slate-950 sm:text-3xl">
                    {{ $exam->title }}
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Build, organize and review the examination question bank.
                </p>

            </div>


            {{-- Exam code --}}
            <button
                type="button"
                @click="copyCode()"
                class="group flex items-center gap-3 border border-slate-200 bg-white px-4 py-3
                       text-left transition-all duration-200 hover:-translate-y-0.5
                       hover:border-indigo-300 hover:shadow-md active:translate-y-0 active:scale-[0.98]">

                <div>
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-400">
                        Access Code
                    </p>

                    <p class="mt-1 font-mono text-sm font-black tracking-[0.12em] text-indigo-600">
                        {{ $exam->exam_code }}
                    </p>
                </div>

                <span
                    x-show="!copied"
                    class="text-slate-400 transition-colors group-hover:text-indigo-600">
                    ⧉
                </span>

                <span
                    x-cloak
                    x-show="copied"
                    class="text-xs font-black text-emerald-600">
                    ✓
                </span>

            </button>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- EXAM WORKSPACE NAVIGATION                              --}}
    {{-- ====================================================== --}}

    <nav class="flex items-center gap-1 overflow-x-auto border-b border-slate-200">

        <a
            href="{{ route('teacher.exams.show', $exam->id) }}"
            class="relative shrink-0 px-4 py-4 text-xs font-bold text-slate-500
                   transition-all hover:text-indigo-600 active:scale-95">
            Overview
        </a>


        <div class="relative shrink-0 px-4 py-4 text-xs font-bold text-indigo-700">

            Questions

            <span class="ml-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[9px] text-indigo-700">
                {{ $totalQuestions }}
            </span>

            <span class="absolute inset-x-3 bottom-0 h-[2px] bg-indigo-600"></span>

        </div>


        <a
            href="{{ route('teacher.exams.show', $exam->id) }}?tab=responses"
            class="relative shrink-0 px-4 py-4 text-xs font-bold text-slate-500
                   transition-all hover:text-indigo-600 active:scale-95">
            Responses
        </a>


        <a
            href="{{ route('teacher.analytics.exam', $exam->id) }}"
            class="group relative shrink-0 px-4 py-4 text-xs font-bold text-slate-500
                   transition-all hover:text-indigo-600 active:scale-95">

            Analysis

            <span class="ml-1 inline-block transition-transform group-hover:translate-x-1">
                ↗
            </span>

        </a>

    </nav>



    {{-- ====================================================== --}}
    {{-- FLASH / VALIDATION                                     --}}
    {{-- ====================================================== --}}

    @if(session('success'))

    <div
        class="flex items-center justify-between border border-emerald-200
                   bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">

        <span>{{ session('success') }}</span>
        <span>✓</span>

    </div>

    @endif


    @if(session('error'))

    <div
        class="border border-rose-200 bg-rose-50 px-4 py-3
                   text-sm font-semibold text-rose-700">
        {{ session('error') }}
    </div>

    @endif


    @if($errors->any())

    <div
        class="border border-rose-200 bg-rose-50 px-4 py-4 text-rose-700">

        <p class="text-xs font-extrabold uppercase tracking-wider">
            Please fix the following
        </p>

        <ul class="mt-2 list-inside list-disc space-y-1 text-xs">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>

    </div>

    @endif



    {{-- ====================================================== --}}
    {{-- QUESTION BUILDER                                       --}}
    {{-- ====================================================== --}}

    <section
        id="questionBuilder"
        class="{{ $errors->any() ? '' : 'hidden' }} overflow-hidden border border-indigo-100 bg-white
               shadow-xl shadow-slate-200/40">


        <div class="grid lg:grid-cols-[260px_minmax(0,1fr)]">


            {{-- Builder intro --}}
            <aside class="relative overflow-hidden bg-slate-950 p-7 text-white">

                <div class="relative z-10">

                    <p class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-indigo-400">
                        Question Builder
                    </p>

                    <h3 class="mt-3 text-2xl font-black tracking-[-0.04em]">
                        Add an assessment item.
                    </h3>

                    <p class="mt-3 text-sm leading-6 text-slate-400">
                        Select the question format and define its correct answer,
                        topic and point value.
                    </p>


                    <div class="mt-8 space-y-4">

                        <div class="flex items-center gap-3">

                            <span class="flex h-7 w-7 items-center justify-center rounded-full
                                         bg-indigo-500/20 text-[10px] font-black text-indigo-300">
                                01
                            </span>

                            <span class="text-xs font-semibold text-slate-300">
                                Choose question type
                            </span>

                        </div>


                        <div class="flex items-center gap-3">

                            <span class="flex h-7 w-7 items-center justify-center rounded-full
                                         bg-white/5 text-[10px] font-black text-slate-500">
                                02
                            </span>

                            <span class="text-xs font-semibold text-slate-500">
                                Add content and answer
                            </span>

                        </div>


                        <div class="flex items-center gap-3">

                            <span class="flex h-7 w-7 items-center justify-center rounded-full
                                         bg-white/5 text-[10px] font-black text-slate-500">
                                03
                            </span>

                            <span class="text-xs font-semibold text-slate-500">
                                Save to question bank
                            </span>

                        </div>

                    </div>

                </div>


                <div
                    class="absolute -bottom-20 -right-20 h-56 w-56
                           rounded-full bg-indigo-600/20 blur-3xl">
                </div>

            </aside>



            {{-- Builder form --}}
            <div class="p-6 sm:p-8">

                <div class="mb-7 flex items-start justify-between">

                    <div>

                        <p class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-slate-400">
                            New Question
                        </p>

                        <h3 class="mt-1 text-lg font-extrabold text-slate-950">
                            Question configuration
                        </h3>

                    </div>


                    <button
                        type="button"
                        onclick="closeQuestionBuilder()"
                        class="flex h-9 w-9 items-center justify-center rounded-full
                               text-slate-400 transition-all hover:rotate-90
                               hover:bg-rose-50 hover:text-rose-600 active:scale-75">
                        ×
                    </button>

                </div>



                <form
                    method="POST"
                    action="{{ route('teacher.questions.store', $exam->id) }}"
                    class="space-y-6">

                    @csrf


                    {{-- Question type --}}
                    <div>

                        <label
                            for="questionType"
                            class="mb-2 block text-[10px] font-extrabold uppercase
                                   tracking-[0.16em] text-slate-500">
                            Question Type
                        </label>


                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">

                            <button
                                type="button"
                                data-question-type="mcq"
                                onclick="selectQuestionType('mcq')"
                                class="question-type-button rounded-xl border border-slate-200 bg-slate-50
                                       px-4 py-3 text-left transition-all duration-200
                                       hover:-translate-y-0.5 hover:border-indigo-300
                                       active:translate-y-0 active:scale-95">

                                <span class="block text-xs font-extrabold text-slate-800">
                                    Multiple Choice
                                </span>

                                <span class="mt-1 block text-[10px] text-slate-400">
                                    Four answer choices
                                </span>

                            </button>


                            <button
                                type="button"
                                data-question-type="tf"
                                onclick="selectQuestionType('tf')"
                                class="question-type-button rounded-xl border border-slate-200 bg-slate-50
                                       px-4 py-3 text-left transition-all duration-200
                                       hover:-translate-y-0.5 hover:border-indigo-300
                                       active:translate-y-0 active:scale-95">

                                <span class="block text-xs font-extrabold text-slate-800">
                                    True / False
                                </span>

                                <span class="mt-1 block text-[10px] text-slate-400">
                                    Binary response
                                </span>

                            </button>


                            <button
                                type="button"
                                data-question-type="identification"
                                onclick="selectQuestionType('identification')"
                                class="question-type-button rounded-xl border border-slate-200 bg-slate-50
                                       px-4 py-3 text-left transition-all duration-200
                                       hover:-translate-y-0.5 hover:border-indigo-300
                                       active:translate-y-0 active:scale-95">

                                <span class="block text-xs font-extrabold text-slate-800">
                                    Identification
                                </span>

                                <span class="mt-1 block text-[10px] text-slate-400">
                                    Written response
                                </span>

                            </button>

                        </div>


                        {{-- Actual submitted value --}}
                        <select
                            name="question_type"
                            id="questionType"
                            class="hidden">

                            <option value="mcq" {{ old('question_type') === 'mcq' ? 'selected' : '' }}>
                                Multiple Choice
                            </option>

                            <option value="tf" {{ old('question_type') === 'tf' ? 'selected' : '' }}>
                                True / False
                            </option>

                            <option
                                value="identification"
                                {{ old('question_type') === 'identification' ? 'selected' : '' }}>
                                Identification
                            </option>

                        </select>

                    </div>



                    {{-- Question --}}
                    <div>

                        <label
                            for="question_text"
                            class="mb-2 block text-[10px] font-extrabold uppercase
                                   tracking-[0.16em] text-slate-500">
                            Question
                        </label>

                        <textarea
                            id="question_text"
                            name="question_text"
                            rows="4"
                            required
                            placeholder="Type the question or prompt..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50
                                   px-4 py-3 text-sm font-medium text-slate-800 outline-none
                                   transition-all placeholder:text-slate-400
                                   focus:border-indigo-500 focus:bg-white
                                   focus:ring-4 focus:ring-indigo-100">{{ old('question_text') }}</textarea>

                    </div>



                    <div class="grid grid-cols-1 gap-5 md:grid-cols-[1fr_140px]">


                        {{-- Topic --}}
                        <div>

                            <label
                                for="topic"
                                class="mb-2 block text-[10px] font-extrabold uppercase
                                       tracking-[0.16em] text-slate-500">
                                Topic
                            </label>

                            <input
                                type="text"
                                id="topic"
                                name="topic"
                                value="{{ old('topic') }}"
                                placeholder="e.g. SQL Joins"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50
                                       px-4 py-3 text-sm font-medium text-slate-800 outline-none
                                       transition-all placeholder:text-slate-400
                                       focus:border-indigo-500 focus:bg-white
                                       focus:ring-4 focus:ring-indigo-100">

                        </div>



                        {{-- Points --}}
                        <div>

                            <label
                                for="points"
                                class="mb-2 block text-[10px] font-extrabold uppercase
                                       tracking-[0.16em] text-slate-500">
                                Points
                            </label>

                            <input
                                type="number"
                                id="points"
                                name="points"
                                value="{{ old('points', 1) }}"
                                min="1"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50
                                       px-4 py-3 text-sm font-bold text-slate-800 outline-none
                                       transition-all focus:border-indigo-500 focus:bg-white
                                       focus:ring-4 focus:ring-indigo-100">

                        </div>

                    </div>



                    {{-- ================================================== --}}
                    {{-- MCQ                                                --}}
                    {{-- ================================================== --}}

                    <div id="mcqSection">

                        <div class="border-t border-slate-200 pt-6">

                            <div class="mb-4">

                                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-500">
                                    Answer Choices
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Enter four choices and select which one is correct.
                                </p>

                            </div>


                            <div class="space-y-3">

                                @foreach(['A', 'B', 'C', 'D'] as $index => $letter)

                                <div class="group flex items-center gap-3">

                                    <span
                                        class="flex h-9 w-9 shrink-0 items-center justify-center
                                                   rounded-full border border-slate-200 bg-white
                                                   text-xs font-black text-slate-500
                                                   transition-colors group-focus-within:border-indigo-500
                                                   group-focus-within:bg-indigo-50
                                                   group-focus-within:text-indigo-600">
                                        {{ $letter }}
                                    </span>

                                    <input
                                        type="text"
                                        name="options[]"
                                        value="{{ old('options.' . $index) }}"
                                        placeholder="Choice {{ $letter }}"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50
                                                   px-4 py-3 text-sm font-medium text-slate-800 outline-none
                                                   transition-all placeholder:text-slate-400
                                                   focus:border-indigo-500 focus:bg-white
                                                   focus:ring-4 focus:ring-indigo-100">

                                </div>

                                @endforeach

                            </div>



                            <div class="mt-5">

                                <label
                                    for="correct_option"
                                    class="mb-2 block text-[10px] font-extrabold uppercase
                                           tracking-[0.16em] text-slate-500">
                                    Correct Choice
                                </label>

                                <select
                                    id="correct_option"
                                    name="correct_option"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50
                                           px-4 py-3 text-sm font-semibold text-slate-700 outline-none
                                           transition-all focus:border-indigo-500 focus:bg-white
                                           focus:ring-4 focus:ring-indigo-100">

                                    <option value="0" {{ old('correct_option') == '0' ? 'selected' : '' }}>
                                        Choice A
                                    </option>

                                    <option value="1" {{ old('correct_option') == '1' ? 'selected' : '' }}>
                                        Choice B
                                    </option>

                                    <option value="2" {{ old('correct_option') == '2' ? 'selected' : '' }}>
                                        Choice C
                                    </option>

                                    <option value="3" {{ old('correct_option') == '3' ? 'selected' : '' }}>
                                        Choice D
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>



                    {{-- ================================================== --}}
                    {{-- TRUE FALSE                                         --}}
                    {{-- ================================================== --}}

                    <div id="tfSection" style="display:none;">

                        <div class="border-t border-slate-200 pt-6">

                            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-500">
                                Correct Answer
                            </p>


                            <div class="mt-3 grid grid-cols-2 gap-3">

                                <label
                                    class="group cursor-pointer rounded-xl border border-slate-200
                                           bg-slate-50 p-4 transition-all hover:-translate-y-0.5
                                           hover:border-indigo-300 has-[:checked]:border-indigo-500
                                           has-[:checked]:bg-indigo-50 active:scale-[0.98]">

                                    <div class="flex items-center gap-3">

                                        <input
                                            type="radio"
                                            name="correct_answer"
                                            value="True"
                                            {{ old('correct_answer') === 'True' ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600">

                                        <span class="text-sm font-extrabold text-slate-800">
                                            True
                                        </span>

                                    </div>

                                </label>


                                <label
                                    class="group cursor-pointer rounded-xl border border-slate-200
                                           bg-slate-50 p-4 transition-all hover:-translate-y-0.5
                                           hover:border-indigo-300 has-[:checked]:border-indigo-500
                                           has-[:checked]:bg-indigo-50 active:scale-[0.98]">

                                    <div class="flex items-center gap-3">

                                        <input
                                            type="radio"
                                            name="correct_answer"
                                            value="False"
                                            {{ old('correct_answer') === 'False' ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600">

                                        <span class="text-sm font-extrabold text-slate-800">
                                            False
                                        </span>

                                    </div>

                                </label>

                            </div>

                        </div>

                    </div>



                    {{-- ================================================== --}}
                    {{-- IDENTIFICATION                                     --}}
                    {{-- ================================================== --}}

                    <div id="idSection" style="display:none;">

                        <div class="border-t border-slate-200 pt-6">

                            <label
                                for="identification_answer"
                                class="mb-2 block text-[10px] font-extrabold uppercase
                                       tracking-[0.16em] text-slate-500">
                                Correct Answer
                            </label>

                            <input
                                type="text"
                                id="identification_answer"
                                name="identification_answer"
                                value="{{ old('identification_answer') }}"
                                placeholder="Expected answer..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50
                                       px-4 py-3 text-sm font-medium text-slate-800 outline-none
                                       transition-all placeholder:text-slate-400
                                       focus:border-indigo-500 focus:bg-white
                                       focus:ring-4 focus:ring-indigo-100">

                        </div>

                    </div>



                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6">

                        <button
                            type="button"
                            onclick="closeQuestionBuilder()"
                            class="rounded-xl px-4 py-2.5 text-xs font-bold text-slate-500
                                   transition-all hover:bg-slate-100 hover:text-slate-800
                                   active:scale-95">
                            Cancel
                        </button>


                        <button
                            type="submit"
                            class="group inline-flex items-center gap-2 rounded-xl bg-indigo-600
                                   px-5 py-2.5 text-xs font-bold text-white
                                   transition-all duration-200 hover:-translate-y-0.5
                                   hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200
                                   active:translate-y-0 active:scale-95">

                            <span class="transition-transform group-hover:rotate-90">
                                +
                            </span>

                            Save Question

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- QUESTION BANK HEADER                                   --}}
    {{-- ====================================================== --}}

    <section>

        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">

            <div>

                <p class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-indigo-600">
                    Question Bank
                </p>

                <h3 class="mt-1 text-xl font-extrabold tracking-tight text-slate-950">
                    {{ $totalQuestions }}
                    {{ Str::plural('question', $totalQuestions) }}
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $totalPoints }} total possible
                    {{ Str::plural('point', $totalPoints) }}.
                </p>

            </div>



            {{-- Search --}}
            <div class="relative w-full lg:w-80">

                <svg
                    class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z" />

                </svg>

                <input
                    type="text"
                    x-model="search"
                    placeholder="Search question or topic..."
                    class="w-full rounded-xl border border-slate-200 bg-white
                           py-2.5 pl-10 pr-4 text-sm font-medium text-slate-700
                           outline-none transition-all placeholder:text-slate-400
                           focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100/60">

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- FILTERS                                                --}}
    {{-- ====================================================== --}}

    <section class="border-y border-slate-200">

        <div class="flex items-center gap-1 overflow-x-auto">

            <button
                type="button"
                @click="filter = 'all'"
                :class="filter === 'all'
                    ? 'text-indigo-700'
                    : 'text-slate-500 hover:text-slate-950'"
                class="relative shrink-0 px-4 py-4 text-xs font-bold transition-all active:scale-95">

                All

                <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[9px]">
                    {{ $totalQuestions }}
                </span>

                <span
                    x-show="filter === 'all'"
                    class="absolute inset-x-3 bottom-0 h-[2px] bg-indigo-600">
                </span>

            </button>


            <button
                type="button"
                @click="filter = 'mcq'"
                :class="filter === 'mcq'
                    ? 'text-indigo-700'
                    : 'text-slate-500 hover:text-slate-950'"
                class="relative shrink-0 px-4 py-4 text-xs font-bold transition-all active:scale-95">

                Multiple Choice

                <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[9px]">
                    {{ $mcqCount }}
                </span>

                <span
                    x-show="filter === 'mcq'"
                    class="absolute inset-x-3 bottom-0 h-[2px] bg-indigo-600">
                </span>

            </button>


            <button
                type="button"
                @click="filter = 'tf'"
                :class="filter === 'tf'
                    ? 'text-indigo-700'
                    : 'text-slate-500 hover:text-slate-950'"
                class="relative shrink-0 px-4 py-4 text-xs font-bold transition-all active:scale-95">

                True / False

                <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[9px]">
                    {{ $tfCount }}
                </span>

                <span
                    x-show="filter === 'tf'"
                    class="absolute inset-x-3 bottom-0 h-[2px] bg-indigo-600">
                </span>

            </button>


            <button
                type="button"
                @click="filter = 'identification'"
                :class="filter === 'identification'
                    ? 'text-indigo-700'
                    : 'text-slate-500 hover:text-slate-950'"
                class="relative shrink-0 px-4 py-4 text-xs font-bold transition-all active:scale-95">

                Identification

                <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[9px]">
                    {{ $identificationCount }}
                </span>

                <span
                    x-show="filter === 'identification'"
                    class="absolute inset-x-3 bottom-0 h-[2px] bg-indigo-600">
                </span>

            </button>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- QUESTIONS                                              --}}
    {{-- ====================================================== --}}

    <section>

        <div class="border-t border-slate-200">

            @forelse($questions as $question)

            @php
            $questionType = strtolower($question->question_type);

            $typeLabel = match($questionType) {
            'mcq' => 'Multiple Choice',
            'tf' => 'True / False',
            'identification' => 'Identification',
            default => strtoupper($questionType),
            };
            @endphp


            <article
                x-show="
                        (filter === 'all' || filter === '{{ $questionType }}') &&
                        (
                            search === '' ||
                            @js(strtolower($question->question_text))
                                .includes(search.toLowerCase()) ||
                            @js(strtolower($question->topic ?? ''))
                                .includes(search.toLowerCase())
                        )
                    "
                x-transition.opacity.duration.200ms
                class="group relative border-b border-slate-200 transition-all duration-300
                           hover:bg-white">


                <div
                    class="absolute bottom-0 left-0 top-0 w-[3px] scale-y-0
                               bg-indigo-600 transition-transform duration-300
                               group-hover:scale-y-100">
                </div>



                {{-- Question row --}}
                <button
                    type="button"
                    @click="openQuestion = openQuestion === {{ $question->id }}
                            ? null
                            : {{ $question->id }}"
                    class="w-full px-2 py-5 text-left transition-all active:scale-[0.995] sm:px-5">

                    <div class="grid grid-cols-[42px_minmax(0,1fr)_auto] items-start gap-3 sm:gap-5">


                        {{-- Number --}}
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-full
                                       bg-slate-100 text-[10px] font-black text-slate-500
                                       transition-all duration-300
                                       group-hover:bg-indigo-50 group-hover:text-indigo-600">

                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}

                        </div>



                        {{-- Content --}}
                        <div class="min-w-0">

                            <p
                                class="text-sm font-bold leading-6 text-slate-850
                                           transition-colors group-hover:text-indigo-700 sm:text-[15px]">
                                {{ $question->question_text }}
                            </p>


                            <div
                                class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1
                                           text-[9px] font-extrabold uppercase tracking-[0.13em]
                                           text-slate-400">

                                @if($question->topic)

                                <span class="text-indigo-500">
                                    {{ $question->topic }}
                                </span>

                                <span class="text-slate-300">•</span>

                                @endif


                                <span>
                                    {{ $typeLabel }}
                                </span>

                                <span class="text-slate-300">•</span>

                                <span>
                                    {{ $question->points }}
                                    {{ Str::plural('point', $question->points) }}
                                </span>

                            </div>

                        </div>



                        {{-- Expand --}}
                        <span
                            class="mt-1 flex h-8 w-8 items-center justify-center rounded-full
                                       border border-slate-200 text-slate-400
                                       transition-all duration-300
                                       group-hover:border-indigo-300 group-hover:text-indigo-600"
                            :class="openQuestion === {{ $question->id }}
                                    ? 'rotate-180 bg-indigo-50 border-indigo-200 text-indigo-600'
                                    : ''">
                            ↓
                        </span>

                    </div>

                </button>



                {{-- ================================================== --}}
                {{-- EXPANDED QUESTION                                  --}}
                {{-- ================================================== --}}

                <div
                    x-cloak
                    x-show="openQuestion === {{ $question->id }}"
                    x-collapse
                    class="px-5 pb-6 sm:pl-[82px]">


                    <div class="border-l-2 border-indigo-100 pl-5">


                        {{-- MCQ --}}
                        @if($questionType === 'mcq')

                        <p
                            class="mb-3 text-[9px] font-extrabold uppercase
                                           tracking-[0.15em] text-slate-400">
                            Answer Choices
                        </p>


                        <div class="space-y-2">

                            @forelse($question->options as $option)

                            @php
                            $optionText =
                            $option->option_text
                            ?? $option->option
                            ?? $option->text
                            ?? $option->answer
                            ?? '';
                            @endphp


                            <div
                                class="flex items-center justify-between gap-4
                                                   rounded-xl border px-4 py-3
                                                   {{ trim((string) $question->correct_answer) === trim((string) $optionText)
                                                        ? 'border-emerald-200 bg-emerald-50'
                                                        : 'border-slate-200 bg-slate-50' }}">

                                <span
                                    class="text-xs font-semibold
                                                       {{ trim((string) $question->correct_answer) === trim((string) $optionText)
                                                            ? 'text-emerald-800'
                                                            : 'text-slate-600' }}">

                                    {{ $optionText }}

                                </span>


                                @if(trim((string) $question->correct_answer) === trim((string) $optionText))

                                <span
                                    class="shrink-0 text-[9px] font-extrabold
                                                           uppercase tracking-wider text-emerald-600">
                                    ✓ Correct
                                </span>

                                @endif

                            </div>

                            @empty

                            <p class="text-xs text-slate-400">
                                No answer options available.
                            </p>

                            @endforelse

                        </div>



                        {{-- True / False --}}
                        @elseif($questionType === 'tf')

                        <p
                            class="text-[9px] font-extrabold uppercase
                                           tracking-[0.15em] text-slate-400">
                            Correct Answer
                        </p>

                        <p class="mt-2 text-sm font-extrabold text-emerald-700">
                            {{ $question->correct_answer }}
                        </p>



                        {{-- Identification --}}
                        @elseif($questionType === 'identification')

                        <p
                            class="text-[9px] font-extrabold uppercase
                                           tracking-[0.15em] text-slate-400">
                            Expected Answer
                        </p>

                        <p class="mt-2 text-sm font-extrabold text-emerald-700">
                            {{ $question->correct_answer }}
                        </p>

                        @endif



                        {{-- Actions --}}
                        <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">

                            <a
                                href="{{ route('teacher.questions.edit', $question->id) }}"
                                class="group/edit inline-flex items-center gap-2 rounded-xl
                                           border border-slate-200 bg-white px-3.5 py-2
                                           text-[10px] font-bold text-slate-600
                                           transition-all duration-200
                                           hover:-translate-y-0.5 hover:border-amber-300
                                           hover:text-amber-700 hover:shadow-sm
                                           active:translate-y-0 active:scale-95">

                                Edit Question

                                <span class="transition-transform group-hover/edit:translate-x-0.5">
                                    →
                                </span>

                            </a>


                            <form
                                method="POST"
                                action="{{ route('teacher.questions.destroy', $question->id) }}"
                                onsubmit="return confirm('Delete this question? This action cannot be undone.')">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="rounded-xl px-3.5 py-2 text-[10px] font-bold
                                               text-rose-600 transition-all
                                               hover:bg-rose-50 hover:text-rose-700
                                               active:scale-95">
                                    Delete
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </article>

            @empty

            <div class="py-20 text-center">

                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center
                               rounded-full bg-indigo-50 text-xl font-light text-indigo-600">
                    +
                </div>

                <h3 class="mt-5 text-lg font-extrabold text-slate-950">
                    Your question bank is empty.
                </h3>

                <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                    Add the first question to begin building this examination.
                </p>

                <button
                    type="button"
                    onclick="openQuestionBuilder()"
                    class="mt-6 rounded-xl bg-indigo-600 px-5 py-2.5
                               text-xs font-bold text-white transition-all
                               hover:-translate-y-0.5 hover:bg-indigo-700
                               hover:shadow-lg active:scale-95">
                    Add First Question
                </button>

            </div>

            @endforelse

        </div>

    </section>

</div>



{{-- ========================================================== --}}
{{-- PAGE SCRIPT                                                --}}
{{-- ========================================================== --}}

<script>
    const questionBuilder =
        document.getElementById('questionBuilder');

    const typeSelect =
        document.getElementById('questionType');

    const mcqSection =
        document.getElementById('mcqSection');

    const tfSection =
        document.getElementById('tfSection');

    const idSection =
        document.getElementById('idSection');


    function openQuestionBuilder() {

        if (!questionBuilder) {
            return;
        }

        questionBuilder.classList.remove('hidden');

        requestAnimationFrame(() => {

            questionBuilder.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

        });

    }


    function closeQuestionBuilder() {

        if (!questionBuilder) {
            return;
        }

        questionBuilder.classList.add('hidden');

    }



    /*
     * Disable fields in inactive question types.
     *
     * This preserves the same important behavior from your
     * original page: hidden answer fields are not submitted.
     */
    function toggleInputs(container, enabled) {

        if (!container) {
            return;
        }

        const fields =
            container.querySelectorAll('input, select');

        fields.forEach(field => {
            field.disabled = !enabled;
        });

    }



    function selectQuestionType(type) {

        if (!typeSelect) {
            return;
        }

        typeSelect.value = type;

        changeType();

    }



    function changeType() {

        if (
            !typeSelect ||
            !mcqSection ||
            !tfSection ||
            !idSection
        ) {
            return;
        }


        mcqSection.style.display = 'none';
        tfSection.style.display = 'none';
        idSection.style.display = 'none';


        toggleInputs(mcqSection, false);
        toggleInputs(tfSection, false);
        toggleInputs(idSection, false);


        if (typeSelect.value === 'mcq') {

            mcqSection.style.display = 'block';
            toggleInputs(mcqSection, true);

        } else if (typeSelect.value === 'tf') {

            tfSection.style.display = 'block';
            toggleInputs(tfSection, true);

        } else if (typeSelect.value === 'identification') {

            idSection.style.display = 'block';
            toggleInputs(idSection, true);

        }


        /*
         * Update visual question-type selector.
         */
        document
            .querySelectorAll('.question-type-button')
            .forEach(button => {

                const active =
                    button.dataset.questionType ===
                    typeSelect.value;


                button.classList.toggle(
                    'border-indigo-500',
                    active
                );

                button.classList.toggle(
                    'bg-indigo-50',
                    active
                );

                button.classList.toggle(
                    'ring-2',
                    active
                );

                button.classList.toggle(
                    'ring-indigo-100',
                    active
                );

            });

    }



    if (typeSelect) {

        /*
         * Default to old input after validation failure.
         * Otherwise MCQ.
         */
        if (!typeSelect.value) {
            typeSelect.value = 'mcq';
        }

        changeType();

    }
</script>

@endsection