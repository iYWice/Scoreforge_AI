{-- Use the application layout and configure inherited page structure. --}
@extends('layouts.app')

{-- Define this page section and its presentation content. --}
@section('title', $exam->title . ' Analysis')
{-- Define this page section and its presentation content. --}
@section('eyebrow', 'Exam Workspace')

{-- Define this page section and its presentation content. --}
@section('page-actions')
<div class="flex items-center gap-2">

    <a
        href="{{ route('teacher.analytics.index') }}"
        class="aiq-button aiq-button-secondary hidden sm:inline-flex items-center gap-2 rounded-xl border border-slate-200
               bg-white px-3.5 py-2.5 text-xs font-bold text-slate-600
               transition-all duration-200 hover:-translate-y-0.5
               hover:border-slate-300 hover:text-slate-950 hover:shadow-sm
               active:translate-y-0 active:scale-95">
        ← Analytics
    </a>

    <a
        href="{{ route('teacher.exams.show', $exam->id) }}"
        class="aiq-button aiq-button-primary inline-flex items-center gap-2 rounded-xl bg-slate-950
               px-4 py-2.5 text-xs font-bold text-white
               transition-all duration-200 hover:-translate-y-0.5
               hover:bg-indigo-600 hover:shadow-lg
               active:translate-y-0 active:scale-95">
        Exam Workspace →
    </a>

</div>

@push('scripts')
<script>
(() => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    document.querySelectorAll('.aiq-exam-page [data-aiq-tilt], .exam-metric-grid > div').forEach(card => {
        card.setAttribute('data-aiq-tilt', '');
        card.addEventListener('pointermove', event => {
            if (event.pointerType === 'touch') return;
            const r = card.getBoundingClientRect();
            card.style.setProperty('--tilt-x', `${(0.5 - (event.clientY-r.top)/r.height)*4}deg`);
            card.style.setProperty('--tilt-y', `${((event.clientX-r.left)/r.width-0.5)*6}deg`);
        });
        card.addEventListener('pointerleave', () => { card.style.setProperty('--tilt-x','0deg'); card.style.setProperty('--tilt-y','0deg'); });
    });
})();
</script>
@endpush

@endsection


{-- Define this page section and its presentation content. --}
@section('content')

<style>
    .aiq-exam-page { --aiq-purple:#7047df; --aiq-ink:#292437; --aiq-pink:#f4a8e9; --aiq-yellow:#ffe16a; color:var(--aiq-ink); }
    .exam-hero { position:relative; overflow:hidden; border:1.5px solid #33253f !important; border-radius:15px; padding:22px; background:linear-gradient(120deg,#ffe16a,#fff1b5 55%,#f2eaff); box-shadow:5px 6px 0 #33253f; }
    .exam-hero h1 { color:#292437 !important; }
    .exam-workspace-nav { border-color:#d9d0e4 !important; }
    .exam-workspace-nav a, .exam-workspace-nav div { transition:color .18s, transform .18s; }
    .exam-workspace-nav a:hover { color:#7047df !important; transform:translateY(-2px); }
    .exam-workspace-nav span { background:#ffe16a !important; }
    .aiq-exam-page [data-aiq-tilt] { --tilt-x:0deg; --tilt-y:0deg; transform:perspective(1000px) rotateX(var(--tilt-x)) rotateY(var(--tilt-y)); transform-style:preserve-3d; transition:transform .16s ease,box-shadow .2s; }
    .exam-metric-grid > div { min-height:120px; padding:14px; border:1.5px solid #382d43 !important; border-radius:10px; background:#fff; box-shadow:0 4px 0 #382d43; }
    .exam-metric-grid > div:nth-child(2) { background:#fff2bd; }
    .exam-metric-grid > div:nth-child(3) { background:#f9e4f6; }
    .exam-metric-grid > div:nth-child(4) { background:#e7f3e5; }
    .exam-metric-grid > div:hover { transform:perspective(1000px) rotateX(var(--tilt-x)) rotateY(var(--tilt-y)) translateY(-4px); box-shadow:0 8px 0 #382d43; }
    .exam-panel { border:1.5px solid #382d43 !important; border-radius:13px; background:#fffdf8 !important; box-shadow:0 4px 0 #382d43; }
    .exam-outcome { border:1.5px solid #30243e; border-radius:13px; background:linear-gradient(145deg,#7047df,#5230b5) !important; box-shadow:5px 6px 0 #30243e; }
    .exam-outcome h2 { color:#fff !important; }
    .exam-outcome .text-indigo-400 { color:#ffe16a !important; }
    .exam-outcome .text-slate-500,.exam-outcome .text-slate-400 { color:#e1d8ff !important; }
    .exam-outcome .border-slate-800 { border-color:#ffffff45 !important; }
    .aiq-exam-page .border.border-slate-200.bg-white { border-color:#382d43 !important; border-radius:12px; box-shadow:0 4px 0 #382d43; }
    .aiq-exam-page table thead { background:#f2eaff !important; color:#5937bd !important; }
    .aiq-exam-page table tbody tr { transition:background .18s; }
    .aiq-exam-page table tbody tr:hover { background:#fff8dd; }
    .aiq-exam-page input:not([type="checkbox"]),.aiq-exam-page select { border-radius:8px; }
    @media(max-width:640px){.exam-hero{padding:16px}.exam-metric-grid > div{min-height:108px;padding:10px}}
    @media(prefers-reduced-motion:reduce){.aiq-exam-page *,.aiq-exam-page *::before,.aiq-exam-page *::after{transition-duration:.01ms!important;animation-duration:.01ms!important}}
</style>
@php

$average = (float) ($analytics['average_score'] ?? 0);
$highest = (float) ($analytics['highest_score'] ?? 0);
$lowest = (float) ($analytics['lowest_score'] ?? 0);
$passRate = (float) ($analytics['pass_rate'] ?? 0);

$safeAverage = max(0, min(100, $average));
$safePassRate = max(0, min(100, $passRate));

$topics = collect($analytics['topic_analysis'] ?? []);
$questions = collect($analytics['question_analysis'] ?? []);

$weakestTopic = $topics->sortBy('accuracy')->first();
$strongestTopic = $topics->sortByDesc('accuracy')->first();

$difficultQuestions = $questions
->filter(fn ($question) => ($question['difficulty'] ?? null) === 'Difficult')
->count();

$moderateQuestions = $questions
->filter(fn ($question) => ($question['difficulty'] ?? null) === 'Moderate')
->count();

$easyQuestions = $questions
->filter(fn ($question) => ($question['difficulty'] ?? null) === 'Easy')
->count();

$examStatus = strtolower($exam->status ?? 'draft');

@endphp


<div
    x-data="{
        section: 'overview',
        difficulty: 'all',
        search: ''
    }"
    class="aiq-exam-page space-y-8">


    {{-- ====================================================== --}}
    {{-- EXAM IDENTITY                                          --}}
    {{-- ====================================================== --}}

    {-- Render this meaningful page or content section. --}
    <section class="exam-hero border-b border-slate-200 pb-7">

        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">

            <div>

                <div class="flex flex-wrap items-center gap-2">

                    {-- Render this content conditionally based on the current view state. --}
                    @if($examStatus === 'published')

                    <span class="inline-flex items-center gap-1.5
                                     text-[10px] font-extrabold uppercase
                                     tracking-[0.16em] text-emerald-700">

                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                        Published

                    </span>

                    {-- Render this content conditionally based on the current view state. --}
                    @elseif($examStatus === 'draft')

                    <span class="inline-flex items-center gap-1.5
                                     text-[10px] font-extrabold uppercase
                                     tracking-[0.16em] text-amber-700">

                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>

                        Draft

                    </span>

                    {-- Render this content conditionally based on the current view state. --}
                    @else

                    <span class="inline-flex items-center gap-1.5
                                     text-[10px] font-extrabold uppercase
                                     tracking-[0.16em] text-slate-500">

                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>

                        {{ ucfirst($examStatus) }}

                    </span>

                    @endif


                    {-- Render this content conditionally based on the current view state. --}
                    @if($exam->subject)

                    <span class="text-slate-300">/</span>

                    <span class="text-xs font-semibold text-slate-500">
                        {{ $exam->subject->name }}
                    </span>

                    @endif


                    {-- Render this content conditionally based on the current view state. --}
                    @if($exam->class)

                    <span class="text-slate-300">/</span>

                    <span class="text-xs font-semibold text-slate-500">
                        {{ $exam->class->name }}
                    </span>

                    @endif

                </div>


                <h1 class="mt-3 text-2xl font-black tracking-[-0.045em]
                           text-slate-950 sm:text-3xl">
                    {{ $exam->title }}
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Examination performance, topic accuracy and item-level analysis.
                </p>

            </div>


            <div class="text-left lg:text-right">

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.18em] text-slate-400">
                    Exam Code
                </p>

                <p class="mt-1 font-mono text-sm font-black
                          tracking-[0.12em] text-indigo-600">
                    {{ $exam->exam_code }}
                </p>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- EXAM WORKSPACE NAV                                     --}}
    {{-- ====================================================== --}}

    {-- Render this navigation or page landmark. --}
    <nav class="exam-workspace-nav flex items-center gap-1 overflow-x-auto
                border-b border-slate-200">

        <a
            href="{{ route('teacher.exams.show', $exam->id) }}"
            class="relative shrink-0 px-4 py-4 text-xs font-bold
                   text-slate-500 transition-all
                   hover:text-indigo-600 active:scale-95">
            Overview
        </a>


        <a
            href="{{ route('teacher.exams.questions', $exam->id) }}"
            class="relative shrink-0 px-4 py-4 text-xs font-bold
                   text-slate-500 transition-all
                   hover:text-indigo-600 active:scale-95">
            Questions
        </a>


        <a
            href="{{ route('teacher.exams.show', $exam->id) }}?tab=responses"
            class="relative shrink-0 px-4 py-4 text-xs font-bold
                   text-slate-500 transition-all
                   hover:text-indigo-600 active:scale-95">
            Responses
        </a>


        <div class="relative shrink-0 px-4 py-4
                    text-xs font-bold text-indigo-700">

            Analysis

            <span class="absolute inset-x-3 bottom-0
                         h-[2px] bg-indigo-600"></span>

        </div>

    </nav>



    {{-- ====================================================== --}}
    {{-- ANALYSIS SUB NAV                                       --}}
    {{-- ====================================================== --}}

    {-- Render this meaningful page or content section. --}
    <section class="flex items-center gap-2 overflow-x-auto">

        <button
            type="button"
            @click="section = 'overview'"
            :class="section === 'overview'
                ? 'bg-slate-950 text-white'
                : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
            class="shrink-0 rounded-full px-4 py-2
                   text-[10px] font-extrabold uppercase
                   tracking-wider transition-all active:scale-95">

            Overview

        </button>


        <button
            type="button"
            @click="section = 'topics'"
            :class="section === 'topics'
                ? 'bg-slate-950 text-white'
                : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
            class="shrink-0 rounded-full px-4 py-2
                   text-[10px] font-extrabold uppercase
                   tracking-wider transition-all active:scale-95">

            Topics

        </button>


        <button
            type="button"
            @click="section = 'items'"
            :class="section === 'items'
                ? 'bg-slate-950 text-white'
                : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
            class="shrink-0 rounded-full px-4 py-2
                   text-[10px] font-extrabold uppercase
                   tracking-wider transition-all active:scale-95">

            Item Analysis

        </button>



    </section>
    {-- Collect and submit the data for this operation. --}
    <form
        method="POST"
        action="{{ route(
        'teacher.analytics.ai-insight',
        $exam
    ) }}"
        x-data="{ generating: false }"
        @submit="generating = true">

        @csrf

        <button
            type="submit"
            :disabled="generating"
            class="group inline-flex items-center
               gap-2 rounded-xl bg-indigo-600
               px-4 py-2.5 text-xs font-bold
               text-white transition-all
               hover:-translate-y-0.5
               hover:bg-indigo-500
               hover:shadow-lg
               hover:shadow-indigo-500/20
               active:scale-95
               disabled:cursor-not-allowed
               disabled:opacity-60">

            <svg
                x-cloak
                x-show="generating"
                class="h-4 w-4 animate-spin"
                viewBox="0 0 24 24"
                fill="none">

                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4">
                </circle>

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4
                   a4 4 0 00-4 4H4z">
                </path>

            </svg>

            <span
                x-text="generating
                ? 'Analyzing Exam...'
                : 'Generate AI Insight'">
            </span>

            <span
                x-show="!generating"
                class="transition-transform
                   group-hover:translate-x-0.5">
                ✦
            </span>

        </button>

    </form>

    </form>


    {{-- ====================================================== --}}
    {{-- OVERVIEW                                               --}}
    {{-- ====================================================== --}}

    <div
        x-show="section === 'overview'"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="space-y-9">


        {{-- ================================================== --}}
        {{-- METRICS                                            --}}
        {{-- ================================================== --}}

        {-- Render this meaningful page or content section. --}
        <section>

            <p class="text-[10px] font-extrabold uppercase
                      tracking-[0.2em] text-slate-400">
                Examination Performance
            </p>


            <div class="exam-metric-grid mt-5 grid grid-cols-2 gap-x-6 gap-y-7
                        border-y border-slate-200 py-7
                        lg:grid-cols-4">


                <div class="border-r border-slate-200 pr-5">

                    <p class="text-[9px] font-extrabold uppercase
                              tracking-[0.18em] text-slate-400">
                        Students
                    </p>

                    <p class="mt-2 text-4xl font-black
                              tracking-[-0.06em] text-slate-950">
                        {{ $analytics['total_students'] }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Completed responses
                    </p>

                </div>



                <div class="border-r border-slate-200 pr-5">

                    <p class="text-[9px] font-extrabold uppercase
                              tracking-[0.18em] text-slate-400">
                        Average
                    </p>

                    <p class="mt-2 text-4xl font-black
                              tracking-[-0.06em] text-indigo-600">

                        {{ number_format($average, 1) }}

                        <span class="text-lg text-indigo-300">
                            %
                        </span>

                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Class performance
                    </p>

                </div>



                <div class="border-r border-slate-200 pr-5">

                    <p class="text-[9px] font-extrabold uppercase
                              tracking-[0.18em] text-slate-400">
                        Highest
                    </p>

                    <p class="mt-2 text-4xl font-black
                              tracking-[-0.06em] text-slate-950">

                        {{ number_format($highest, 1) }}

                        <span class="text-lg text-slate-300">
                            %
                        </span>

                    </p>

                    <p class="mt-1 text-xs text-emerald-600">
                        Best result
                    </p>

                </div>



                <div>

                    <p class="text-[9px] font-extrabold uppercase
                              tracking-[0.18em] text-slate-400">
                        Lowest
                    </p>

                    <p class="mt-2 text-4xl font-black
                              tracking-[-0.06em] text-slate-950">

                        {{ number_format($lowest, 1) }}

                        <span class="text-lg text-slate-300">
                            %
                        </span>

                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Lowest result
                    </p>

                </div>

            </div>

        </section>



        {{-- ================================================== --}}
        {{-- PERFORMANCE + OUTCOMES                             --}}
        {{-- ================================================== --}}

        {-- Render this meaningful page or content section. --}
        <section class="grid grid-cols-1 gap-6 lg:grid-cols-12">


            {{-- Performance --}}
            <div class="exam-panel border border-slate-200 bg-white
                        p-6 lg:col-span-7 aiq-panel aiq-panel-interactive" data-aiq-tilt>

                <div>

                    <p class="text-[10px] font-extrabold uppercase
                              tracking-[0.2em] text-indigo-600">
                        Class Performance
                    </p>

                    <h2 class="mt-2 text-lg font-extrabold text-slate-950">
                        Examination score
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Average score across completed responses.
                    </p>

                </div>


                <div class="mt-10">

                    <div class="flex items-end justify-between gap-5">

                        <div>

                            <p class="text-5xl font-black
                                      tracking-[-0.07em] text-slate-950">

                                {{ number_format($average, 1) }}

                                <span class="text-xl text-slate-300">
                                    %
                                </span>

                            </p>

                            <p class="mt-2 text-xs font-semibold
                                      text-slate-400">
                                Class average
                            </p>

                        </div>


                        <div class="text-right">

                            <p class="text-[9px] font-extrabold
                                      uppercase tracking-[0.16em]
                                      text-slate-400">
                                Pass Rate
                            </p>

                            <p class="mt-1 text-xl font-black
                                      text-indigo-600">
                                {{ number_format($passRate, 1) }}%
                            </p>

                        </div>

                    </div>


                    <div class="mt-8">

                        <div class="h-2 overflow-hidden bg-slate-100">

                            <div
                                class="h-full bg-indigo-600
                                       transition-all duration-1000"
                                style="width: {{ $safeAverage }}%">
                            </div>

                        </div>


                        <div class="mt-2 flex justify-between
                                    text-[9px] font-bold text-slate-300">

                            <span>0</span>
                            <span>25</span>
                            <span>50</span>
                            <span>75</span>
                            <span>100</span>

                        </div>

                    </div>

                </div>

            </div>



            {{-- Outcome --}}
            <div class="exam-outcome bg-slate-950 p-6 text-white
                        lg:col-span-5" data-aiq-tilt>

                <p class="text-[10px] font-extrabold uppercase
                          tracking-[0.2em] text-indigo-400">
                    Outcome
                </p>

                <h2 class="mt-2 text-lg font-extrabold">
                    Pass / fail distribution
                </h2>


                <div class="mt-8 grid grid-cols-2 gap-4">

                    <div class="border-r border-slate-800">

                        <p class="text-[9px] font-extrabold uppercase
                                  tracking-[0.16em] text-slate-500">
                            Passed
                        </p>

                        <p class="mt-2 text-4xl font-black
                                  tracking-[-0.05em]
                                  text-emerald-400">
                            {{ $analytics['passed'] }}
                        </p>

                    </div>


                    <div class="pl-2">

                        <p class="text-[9px] font-extrabold uppercase
                                  tracking-[0.16em] text-slate-500">
                            Failed
                        </p>

                        <p class="mt-2 text-4xl font-black
                                  tracking-[-0.05em]
                                  text-rose-400">
                            {{ $analytics['failed'] }}
                        </p>

                    </div>

                </div>


                <div class="mt-8 border-t border-slate-800 pt-5">

                    <div class="flex items-center justify-between">

                        <span class="text-xs font-semibold text-slate-400">
                            Pass rate
                        </span>

                        <span class="text-sm font-black text-white">
                            {{ number_format($passRate, 1) }}%
                        </span>

                    </div>


                    <div class="mt-4">

                        <div class="h-1.5 overflow-hidden bg-slate-800">

                            <div
                                class="h-full bg-emerald-400
                                       transition-all duration-1000"
                                style="width: {{ $safePassRate }}%">
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>



        {{-- ================================================== --}}
        {{-- SIGNALS                                            --}}
        {{-- ================================================== --}}

        {-- Render this meaningful page or content section. --}
        <section>

            <div class="mb-5">

                <p class="text-[10px] font-extrabold uppercase
                          tracking-[0.2em] text-slate-400">
                    Performance Signals
                </p>

                <h2 class="mt-1 text-lg font-extrabold text-slate-950">
                    Key examination patterns
                </h2>

            </div>


            <div class="signal-grid grid grid-cols-1 gap-px
                        overflow-hidden border border-slate-200
                        bg-slate-200 md:grid-cols-3">


                {{-- Weakest topic --}}
                <button
                    type="button"
                    @click="section = 'topics'"
                    class="group bg-white p-5 text-left
                           transition-all hover:bg-rose-50/50
                           active:scale-[0.99] aiq-panel aiq-panel-interactive">

                    <p class="text-[9px] font-extrabold uppercase
                              tracking-[0.16em] text-rose-500">
                        Needs Attention
                    </p>

                    {-- Render this content conditionally based on the current view state. --}
                    @if($weakestTopic)

                    <p class="mt-3 text-sm font-extrabold text-slate-900">
                        {{ $weakestTopic['topic'] }}
                    </p>

                    <p class="mt-1 text-2xl font-black text-rose-600">
                        {{ number_format($weakestTopic['accuracy'], 1) }}%
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Lowest topic accuracy
                    </p>

                    {-- Render this content conditionally based on the current view state. --}
                    @else

                    <p class="mt-3 text-sm font-semibold text-slate-400">
                        No topic data
                    </p>

                    @endif

                </button>



                {{-- Strongest topic --}}
                <button
                    type="button"
                    @click="section = 'topics'"
                    class="group bg-white p-5 text-left
                           transition-all hover:bg-emerald-50/50
                           active:scale-[0.99] aiq-panel aiq-panel-interactive">

                    <p class="text-[9px] font-extrabold uppercase
                              tracking-[0.16em] text-emerald-600">
                        Strongest Topic
                    </p>

                    {-- Render this content conditionally based on the current view state. --}
                    @if($strongestTopic)

                    <p class="mt-3 text-sm font-extrabold text-slate-900">
                        {{ $strongestTopic['topic'] }}
                    </p>

                    <p class="mt-1 text-2xl font-black text-emerald-600">
                        {{ number_format($strongestTopic['accuracy'], 1) }}%
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Highest topic accuracy
                    </p>

                    {-- Render this content conditionally based on the current view state. --}
                    @else

                    <p class="mt-3 text-sm font-semibold text-slate-400">
                        No topic data
                    </p>

                    @endif

                </button>



                {{-- Difficult items --}}
                <button
                    type="button"
                    @click="difficulty = 'Difficult'; section = 'items'"
                    class="group bg-white p-5 text-left
                           transition-all hover:bg-amber-50/50
                           active:scale-[0.99] aiq-panel aiq-panel-interactive">

                    <p class="text-[9px] font-extrabold uppercase
                              tracking-[0.16em] text-amber-600">
                        Difficult Items
                    </p>

                    <p class="mt-3 text-3xl font-black text-slate-950">
                        {{ $difficultQuestions }}
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Questions below the difficult-item threshold
                    </p>

                </button>

            </div>

        </section>

    </div>



    {{-- ====================================================== --}}
    {{-- TOPICS TAB                                             --}}
    {{-- ====================================================== --}}

    <div
        x-cloak
        x-show="section === 'topics'"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0">


        {-- Render this meaningful page or content section. --}
        <section>

            <div class="mb-6">

                <p class="text-[10px] font-extrabold uppercase
                          tracking-[0.2em] text-indigo-600">
                    Topic Performance
                </p>

                <h2 class="mt-1 text-xl font-extrabold text-slate-950">
                    Accuracy by topic
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Lower-performing topics appear first.
                </p>

            </div>


            <div class="border-t border-slate-200">

                {-- Iterate over the related records and render each result. --}
                @forelse($topics as $topic)

                @php
                $topicAccuracy = (float) $topic['accuracy'];
                $safeTopicAccuracy = max(0, min(100, $topicAccuracy));
                @endphp


                <div
                    class="group grid gap-5 border-b
                               border-slate-200 py-5
                               transition-colors hover:bg-white
                               md:grid-cols-[minmax(0,1fr)_130px_260px]
                               md:items-center">


                    <div>

                        <p class="text-sm font-extrabold text-slate-850">
                            {{ $topic['topic'] }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">

                            {{ $topic['correct'] }}
                            correct out of
                            {{ $topic['total'] }}

                        </p>

                    </div>



                    <div>

                        <span
                            class="text-lg font-black
                                {{ $topicAccuracy < 50
                                    ? 'text-rose-600'
                                    : ($topicAccuracy < 75
                                        ? 'text-amber-600'
                                        : 'text-emerald-600') }}">

                            {{ number_format($topicAccuracy, 1) }}%

                        </span>

                    </div>



                    <div>

                        <div class="h-1.5 overflow-hidden bg-slate-100">

                            <div
                                class="h-full transition-all duration-700
                                    {{ $topicAccuracy < 50
                                        ? 'bg-rose-500'
                                        : ($topicAccuracy < 75
                                            ? 'bg-amber-500'
                                            : 'bg-emerald-500') }}"
                                style="width: {{ $safeTopicAccuracy }}%">
                            </div>

                        </div>

                    </div>

                </div>

                @empty

                <div class="border-b border-slate-200 py-16 text-center">

                    <p class="text-sm font-bold text-slate-600">
                        No topic data available.
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Assign topics to examination questions
                        to generate topic-level analysis.
                    </p>

                </div>

                @endforelse

            </div>

        </section>

    </div>



    {{-- ====================================================== --}}
    {{-- ITEM ANALYSIS TAB                                      --}}
    {{-- ====================================================== --}}

    <div
        x-cloak
        x-show="section === 'items'"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="space-y-6">


        {-- Render this meaningful page or content section. --}
        <section>

            <div class="flex flex-col gap-5
                        lg:flex-row lg:items-end
                        lg:justify-between">

                <div>

                    <p class="text-[10px] font-extrabold uppercase
                              tracking-[0.2em] text-indigo-600">
                        Item Analysis
                    </p>

                    <h2 class="mt-1 text-xl font-extrabold text-slate-950">
                        Question performance
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Review accuracy and difficulty for each examination item.
                    </p>

                </div>


                {{-- Search --}}
                <div class="relative w-full lg:w-80">

                    <svg
                        class="absolute left-3.5 top-1/2 h-4 w-4
                               -translate-y-1/2 text-slate-400"
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
                        class="w-full rounded-xl border border-slate-200
                               bg-white py-2.5 pl-10 pr-4
                               text-sm font-medium text-slate-700
                               outline-none transition-all
                               placeholder:text-slate-400
                               focus:border-indigo-400
                               focus:ring-4 focus:ring-indigo-100/60 aiq-field">

                </div>

            </div>

        </section>



        {{-- Difficulty filters --}}
        <section class="flex items-center gap-2 overflow-x-auto">

            <button
                type="button"
                @click="difficulty = 'all'"
                :class="difficulty === 'all'
                    ? 'bg-slate-950 text-white'
                    : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                class="shrink-0 rounded-full px-4 py-2
                       text-[10px] font-extrabold uppercase
                       tracking-wider transition-all active:scale-95">

                All {{ $questions->count() }}

            </button>


            <button
                type="button"
                @click="difficulty = 'Difficult'"
                :class="difficulty === 'Difficult'
                    ? 'bg-rose-600 text-white'
                    : 'bg-rose-50 text-rose-600 hover:bg-rose-100'"
                class="shrink-0 rounded-full px-4 py-2
                       text-[10px] font-extrabold uppercase
                       tracking-wider transition-all active:scale-95">

                Difficult {{ $difficultQuestions }}

            </button>


            <button
                type="button"
                @click="difficulty = 'Moderate'"
                :class="difficulty === 'Moderate'
                    ? 'bg-amber-500 text-white'
                    : 'bg-amber-50 text-amber-700 hover:bg-amber-100'"
                class="shrink-0 rounded-full px-4 py-2
                       text-[10px] font-extrabold uppercase
                       tracking-wider transition-all active:scale-95">

                Moderate {{ $moderateQuestions }}

            </button>


            <button
                type="button"
                @click="difficulty = 'Easy'"
                :class="difficulty === 'Easy'
                    ? 'bg-emerald-600 text-white'
                    : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'"
                class="shrink-0 rounded-full px-4 py-2
                       text-[10px] font-extrabold uppercase
                       tracking-wider transition-all active:scale-95">

                Easy {{ $easyQuestions }}

            </button>

        </section>



        {{-- Table --}}
        <section class="overflow-hidden border border-slate-200 bg-white">

            <div class="overflow-x-auto">

                {-- Present structured records in a table. --}
                <table class="w-full border-collapse text-left">

                    {-- Present structured records in a table. --}
                    <thead>

                        <tr
                            class="border-b border-slate-200
                                   bg-slate-50 text-[9px]
                                   font-extrabold uppercase
                                   tracking-[0.15em] text-slate-400">

                            <th class="px-5 py-3.5">
                                #
                            </th>

                            <th class="px-5 py-3.5">
                                Question
                            </th>

                            <th class="px-5 py-3.5">
                                Topic
                            </th>

                            <th class="px-5 py-3.5">
                                Correct
                            </th>

                            <th class="px-5 py-3.5">
                                Incorrect
                            </th>

                            <th class="px-5 py-3.5">
                                Accuracy
                            </th>

                            <th class="px-5 py-3.5">
                                Difficulty
                            </th>

                        </tr>

                    </thead>


                    {-- Present structured records in a table. --}
                    <tbody>

                        {-- Iterate over the related records and render each result. --}
                        @forelse($questions as $question)

                        @php
                        $difficultyLabel =
                        $question['difficulty'] ?? 'Unknown';

                        $questionText =
                        $question['question_text'] ?? '';

                        $questionTopic =
                        $question['topic'] ?? '';

                        $questionAccuracy =
                        (float) ($question['accuracy'] ?? 0);
                        @endphp


                        <tr
                            x-show="
                                    (difficulty === 'all' ||
                                     difficulty === @js($difficultyLabel))
                                    &&
                                    (
                                        search === '' ||
                                        @js(strtolower($questionText))
                                            .includes(search.toLowerCase()) ||
                                        @js(strtolower($questionTopic))
                                            .includes(search.toLowerCase())
                                    )
                                "
                            x-transition.opacity
                            class="group border-b border-slate-100
                                       transition-colors last:border-0
                                       hover:bg-indigo-50/30">


                            <td class="px-5 py-4">

                                <span class="text-xs font-black text-slate-400">
                                    {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                </span>

                            </td>



                            <td class="max-w-sm px-5 py-4">

                                <p class="text-xs font-semibold
                                              leading-5 text-slate-750">
                                    {{ $questionText }}
                                </p>

                            </td>



                            <td class="px-5 py-4">

                                {-- Render this content conditionally based on the current view state. --}
                                @if($questionTopic)

                                <span
                                    class="text-[10px] font-bold
                                                   text-indigo-600">
                                    {{ $questionTopic }}
                                </span>

                                {-- Render this content conditionally based on the current view state. --}
                                @else

                                <span class="text-xs text-slate-300">
                                    —
                                </span>

                                @endif

                            </td>



                            <td class="px-5 py-4">

                                <span class="text-xs font-black
                                                 text-emerald-600">
                                    {{ $question['correct'] }}
                                </span>

                            </td>



                            <td class="px-5 py-4">

                                <span class="text-xs font-black
                                                 text-rose-500">
                                    {{ $question['incorrect'] }}
                                </span>

                            </td>



                            <td class="min-w-[150px] px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="h-1.5 w-16
                                                    overflow-hidden bg-slate-100">

                                        <div
                                            class="h-full transition-all
                                                {{ $questionAccuracy < 50
                                                    ? 'bg-rose-500'
                                                    : ($questionAccuracy < 80
                                                        ? 'bg-amber-500'
                                                        : 'bg-emerald-500') }}"
                                            style="width:
                                                    {{ max(0, min(100, $questionAccuracy)) }}%">
                                        </div>

                                    </div>

                                    <span class="text-xs font-black text-slate-700">
                                        {{ number_format($questionAccuracy, 1) }}%
                                    </span>

                                </div>

                            </td>



                            <td class="px-5 py-4">

                                {-- Render this content conditionally based on the current view state. --}
                                @if($difficultyLabel === 'Easy')

                                <span
                                    class="inline-flex rounded-full
                                                   bg-emerald-50 px-2.5 py-1
                                                   text-[9px] font-extrabold
                                                   uppercase tracking-wider
                                                   text-emerald-700">
                                    Easy
                                </span>

                                {-- Render this content conditionally based on the current view state. --}
                                @elseif($difficultyLabel === 'Moderate')

                                <span
                                    class="inline-flex rounded-full
                                                   bg-amber-50 px-2.5 py-1
                                                   text-[9px] font-extrabold
                                                   uppercase tracking-wider
                                                   text-amber-700">
                                    Moderate
                                </span>

                                {-- Render this content conditionally based on the current view state. --}
                                @elseif($difficultyLabel === 'Difficult')

                                <span
                                    class="inline-flex rounded-full
                                                   bg-rose-50 px-2.5 py-1
                                                   text-[9px] font-extrabold
                                                   uppercase tracking-wider
                                                   text-rose-700">
                                    Difficult
                                </span>

                                {-- Render this content conditionally based on the current view state. --}
                                @else

                                <span
                                    class="inline-flex rounded-full
                                                   bg-slate-100 px-2.5 py-1
                                                   text-[9px] font-extrabold
                                                   uppercase tracking-wider
                                                   text-slate-500">
                                    {{ $difficultyLabel }}
                                </span>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-16 text-center">

                                <p class="text-sm font-bold text-slate-600">
                                    No question analysis available.
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Item analysis will appear after
                                    students complete this examination.
                                </p>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    </div>

</div>

@endsection