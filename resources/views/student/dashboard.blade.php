@extends('layouts.app')

@section('title', 'Student Overview')
@section('eyebrow', 'Student Workspace')

@section('content')

@php

$averageScore = $analytics
? (float) $analytics->average_score
: 0;

$improvementRate = $analytics
? (float) $analytics->improvement_rate
: 0;

$safeAverage = max(0, min(100, $averageScore));

$subjects = collect($performance['subjects'] ?? []);

$weakTopics = collect($performance['weak_topics'] ?? [])
->take(5);

$strongTopics = collect($performance['strong_topics'] ?? [])
->take(5);

$strongestTopic = $strongTopics->first();
$weakestTopic = $weakTopics->first();

@endphp


<div
    x-data="{
        examPanel: false,
        performanceOpen: true
    }"
    class="space-y-10">


    {{-- ====================================================== --}}
    {{-- STUDENT INTRO                                          --}}
    {{-- ====================================================== --}}

    <section class="border-b border-slate-200 pb-8">

        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">

            <div>

                <p class="text-[10px] font-extrabold uppercase
                          tracking-[0.22em] text-indigo-600">
                    Academic Pulse
                </p>

                <h1 class="mt-2 text-3xl font-black tracking-[-0.055em]
                           text-slate-950 sm:text-4xl">

                    Good day, {{ auth()->user()->fname }}.

                </h1>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                    Track your examination performance, identify areas
                    for improvement, and access your assessments.
                </p>

            </div>


            <div class="flex items-center gap-4">

                <div class="hidden text-right sm:block">

                    <p class="text-[9px] font-extrabold uppercase
                              tracking-[0.18em] text-slate-400">
                        Today
                    </p>

                    <p class="mt-1 text-xs font-bold text-slate-700">
                        {{ now()->format('F d, Y') }}
                    </p>

                </div>


                <button
                    type="button"
                    @click="examPanel = !examPanel"
                    class="group inline-flex items-center gap-2
                           rounded-xl bg-indigo-600 px-4 py-2.5
                           text-xs font-bold text-white
                           shadow-sm transition-all duration-200
                           hover:-translate-y-0.5 hover:bg-indigo-500
                           hover:shadow-lg hover:shadow-indigo-500/20
                           active:translate-y-0 active:scale-95">

                    Take Exam

                    <span
                        class="transition-transform duration-200"
                        :class="examPanel ? 'rotate-45' : 'group-hover:translate-x-0.5'">
                        →
                    </span>

                </button>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- EXAM CODE PANEL                                        --}}
    {{-- ====================================================== --}}

    <section
        x-cloak
        x-show="examPanel"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-3"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-3"
        class="overflow-hidden border border-indigo-200
               bg-indigo-50/50">


        <div class="grid lg:grid-cols-[1fr_430px]">

            <div class="p-6 sm:p-8">

                <p class="text-[10px] font-extrabold uppercase
                          tracking-[0.2em] text-indigo-600">
                    Examination Access
                </p>

                <h2 class="mt-2 text-xl font-black
                           tracking-tight text-slate-950">
                    Enter your exam code
                </h2>

                <p class="mt-2 max-w-xl text-sm leading-6 text-slate-500">
                    Use the unique examination code provided by your
                    instructor to access an available assessment.
                </p>

            </div>


            <div class="border-t border-indigo-100 bg-white
                        p-6 lg:border-l lg:border-t-0">

                @if(session('error'))

                <div class="mb-4 border border-rose-200
                                bg-rose-50 px-3.5 py-2.5
                                text-xs font-semibold text-rose-700">

                    {{ session('error') }}

                </div>

                @endif


                <form
                    method="POST"
                    action="{{ route('student.exam.start') }}"
                    class="space-y-3">

                    @csrf


                    <label
                        for="exam_code"
                        class="block text-[9px] font-extrabold
                               uppercase tracking-[0.16em]
                               text-slate-500">

                        Exam Code

                    </label>


                    <div class="flex flex-col gap-2 sm:flex-row">

                        <input
                            type="text"
                            id="exam_code"
                            name="exam_code"
                            placeholder="EXAM-8920"
                            autocomplete="off"
                            required
                            class="min-w-0 flex-1 rounded-xl border
                                   border-slate-200 bg-slate-50
                                   px-4 py-3 font-mono text-sm
                                   font-black uppercase tracking-[0.12em]
                                   text-slate-800 outline-none
                                   transition-all
                                   placeholder:font-sans
                                   placeholder:font-medium
                                   placeholder:tracking-normal
                                   placeholder:text-slate-300
                                   focus:border-indigo-400
                                   focus:bg-white
                                   focus:ring-4
                                   focus:ring-indigo-100/60">


                        <button
                            type="submit"
                            class="group inline-flex shrink-0
                                   items-center justify-center gap-2
                                   rounded-xl bg-slate-950
                                   px-5 py-3 text-xs font-bold
                                   text-white transition-all
                                   hover:bg-indigo-600
                                   active:scale-95">

                            Continue

                            <span class="transition-transform
                                         group-hover:translate-x-1">
                                →
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- CORE METRICS                                           --}}
    {{-- ====================================================== --}}

    <section>

        <div class="grid grid-cols-2 gap-x-6 gap-y-7
                    border-y border-slate-200 py-7
                    lg:grid-cols-4">


            {{-- Average --}}
            <div class="border-r border-slate-200 pr-5">

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.18em] text-slate-400">
                    Average
                </p>

                <p class="mt-2 text-4xl font-black
                          tracking-[-0.06em] text-slate-950">

                    {{ number_format($averageScore, 1) }}

                    <span class="text-lg text-slate-300">
                        %
                    </span>

                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Overall performance
                </p>

            </div>



            {{-- Improvement --}}
            <div class="border-r border-slate-200 pr-5">

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.18em] text-slate-400">
                    Improvement
                </p>

                <p class="mt-2 text-4xl font-black
                          tracking-[-0.06em]
                          {{ $improvementRate > 0
                                ? 'text-emerald-600'
                                : ($improvementRate < 0
                                    ? 'text-rose-600'
                                    : 'text-slate-950') }}">

                    @if($improvementRate > 0)
                    +
                    @endif

                    {{ number_format($improvementRate, 1) }}

                    <span class="text-lg opacity-40">
                        %
                    </span>

                </p>

                <p class="mt-1 text-xs text-slate-400">
                    From previous exam
                </p>

            </div>



            {{-- Exams --}}
            <div class="border-r border-slate-200 pr-5">

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.18em] text-slate-400">
                    Exams Taken
                </p>

                <p class="mt-2 text-4xl font-black
                          tracking-[-0.06em] text-slate-950">
                    {{ $attempts->count() }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Completed assessments
                </p>

            </div>



            {{-- Rank --}}
            <div>

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.18em] text-slate-400">
                    Rank
                </p>

                <p class="mt-2 text-4xl font-black
                          tracking-[-0.06em] text-indigo-600">

                    {{ $analytics?->rank_position
                        ? '#' . $analytics->rank_position
                        : '—' }}

                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Based on average
                </p>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- PERFORMANCE                                            --}}
    {{-- ====================================================== --}}

    <section class="grid grid-cols-1 gap-6 lg:grid-cols-12">


        {{-- Main performance --}}
        <div class="border border-slate-200 bg-white
                    p-6 lg:col-span-8">

            <div class="flex items-start justify-between gap-4">

                <div>

                    <p class="text-[10px] font-extrabold uppercase
                              tracking-[0.2em] text-indigo-600">
                        Your Performance
                    </p>

                    <h2 class="mt-2 text-lg font-extrabold text-slate-950">
                        Subject overview
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Average examination performance by subject.
                    </p>

                </div>


                <button
                    type="button"
                    @click="performanceOpen = !performanceOpen"
                    class="rounded-lg px-2 py-1 text-xs font-bold
                           text-slate-400 transition-all
                           hover:bg-slate-100 hover:text-slate-700
                           active:scale-95">

                    <span x-text="performanceOpen ? 'Hide' : 'Show'"></span>

                </button>

            </div>



            <div
                x-show="performanceOpen"
                x-collapse
                class="mt-7">


                @if($subjects->isNotEmpty())

                <div class="space-y-6">

                    @foreach($subjects as $subject)

                    @php

                    $subjectPercentage =
                    (float) $subject['average_percentage'];

                    $safeSubjectPercentage =
                    max(0, min(100, $subjectPercentage));

                    @endphp


                    <div>

                        <div class="mb-2 flex items-end
                                            justify-between gap-4">

                            <div>

                                <p class="text-xs font-bold
                                                  text-slate-750">
                                    {{ $subject['subject_name'] }}
                                </p>

                                <p class="mt-1 text-[10px]
                                                  font-semibold text-slate-400">

                                    {{ $subject['attempts'] }}

                                    {{ \Illuminate\Support\Str::plural(
                                                'exam',
                                                $subject['attempts']
                                            ) }}

                                </p>

                            </div>


                            <span class="text-sm font-black
                                                 text-slate-900">
                                {{ number_format($subjectPercentage, 1) }}%
                            </span>

                        </div>


                        <div class="h-1.5 overflow-hidden bg-slate-100">

                            <div
                                class="h-full bg-indigo-600
                                               transition-all duration-1000"
                                style="width: {{ $safeSubjectPercentage }}%">
                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>

                @else

                <div class="border-y border-slate-100 py-10 text-center">

                    <p class="text-sm font-bold text-slate-600">
                        No subject performance yet.
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Complete an examination to begin tracking
                        your performance.
                    </p>

                </div>

                @endif

            </div>

        </div>



        {{-- Focus --}}
        <div class="bg-slate-950 p-6 text-white
                    lg:col-span-4">

            <p class="text-[10px] font-extrabold uppercase
                      tracking-[0.2em] text-indigo-400">
                Academic Focus
            </p>

            <h2 class="mt-2 text-lg font-extrabold">
                Performance signals
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-400">
                Your strongest and lowest-performing topics
                based on completed examinations.
            </p>


            <div class="mt-8 space-y-6">


                {{-- Strongest --}}
                <div>

                    <p class="text-[9px] font-extrabold uppercase
                              tracking-[0.16em] text-emerald-400">
                        Strongest
                    </p>

                    @if($strongestTopic)

                    <div class="mt-2 flex items-end justify-between gap-3">

                        <p class="text-sm font-bold text-white">
                            {{ $strongestTopic['topic'] }}
                        </p>

                        <span class="text-xl font-black text-emerald-400">
                            {{ number_format($strongestTopic['percentage'], 1) }}%
                        </span>

                    </div>

                    @else

                    <p class="mt-2 text-sm text-slate-500">
                        No topic data yet.
                    </p>

                    @endif

                </div>



                <div class="border-t border-slate-800"></div>



                {{-- Focus next --}}
                <div>

                    <p class="text-[9px] font-extrabold uppercase
                              tracking-[0.16em] text-rose-400">
                        Focus Next
                    </p>

                    @if($weakestTopic)

                    <div class="mt-2 flex items-end justify-between gap-3">

                        <p class="text-sm font-bold text-white">
                            {{ $weakestTopic['topic'] }}
                        </p>

                        <span class="text-xl font-black text-rose-400">
                            {{ number_format($weakestTopic['percentage'], 1) }}%
                        </span>

                    </div>

                    @else

                    <p class="mt-2 text-sm text-slate-500">
                        No topic data yet.
                    </p>

                    @endif

                </div>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- TOPIC DETAILS                                          --}}
    {{-- ====================================================== --}}

    @if($weakTopics->isNotEmpty() || $strongTopics->isNotEmpty())

    <section>

        <div class="mb-5">

            <p class="text-[10px] font-extrabold uppercase
                          tracking-[0.2em] text-slate-400">
                Topic Analysis
            </p>

            <h2 class="mt-1 text-lg font-extrabold text-slate-950">
                Strengths and areas for review
            </h2>

        </div>


        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">


            {{-- Weak --}}
            <div class="border-t-2 border-rose-500 bg-white">

                <div class="border-b border-slate-200 px-5 py-4">

                    <p class="text-[9px] font-extrabold uppercase
                                  tracking-[0.16em] text-rose-600">
                        Areas for Review
                    </p>

                </div>


                <div class="divide-y divide-slate-100">

                    @forelse($weakTopics as $topic)

                    <div
                        class="flex items-center justify-between
                                       gap-4 px-5 py-4
                                       transition-colors
                                       hover:bg-rose-50/40">

                        <div>

                            <p class="text-xs font-bold text-slate-750">
                                {{ $topic['topic'] }}
                            </p>

                            <p class="mt-1 text-[10px] text-slate-400">
                                {{ $topic['incorrect'] }}
                                incorrect out of
                                {{ $topic['total'] }}
                            </p>

                        </div>


                        <span class="text-xs font-black text-rose-600">
                            {{ number_format($topic['percentage'], 1) }}%
                        </span>

                    </div>

                    @empty

                    <div class="px-5 py-8 text-center
                                        text-xs text-slate-400">
                        No weak topics identified.
                    </div>

                    @endforelse

                </div>

            </div>



            {{-- Strong --}}
            <div class="border-t-2 border-emerald-500 bg-white">

                <div class="border-b border-slate-200 px-5 py-4">

                    <p class="text-[9px] font-extrabold uppercase
                                  tracking-[0.16em] text-emerald-600">
                        Strong Areas
                    </p>

                </div>


                <div class="divide-y divide-slate-100">

                    @forelse($strongTopics as $topic)

                    <div
                        class="flex items-center justify-between
                                       gap-4 px-5 py-4
                                       transition-colors
                                       hover:bg-emerald-50/40">

                        <div>

                            <p class="text-xs font-bold text-slate-750">
                                {{ $topic['topic'] }}
                            </p>

                            <p class="mt-1 text-[10px] text-slate-400">
                                {{ $topic['correct'] }}
                                correct out of
                                {{ $topic['total'] }}
                            </p>

                        </div>


                        <span class="text-xs font-black
                                             text-emerald-600">
                            {{ number_format($topic['percentage'], 1) }}%
                        </span>

                    </div>

                    @empty

                    <div class="px-5 py-8 text-center
                                        text-xs text-slate-400">
                        No strong topics identified.
                    </div>

                    @endforelse

                </div>

            </div>

        </div>

    </section>

    @endif



    {{-- ====================================================== --}}
    {{-- RECENT EXAMS                                           --}}
    {{-- ====================================================== --}}

    <section>

        <div class="mb-5 flex items-end justify-between gap-4">

            <div>

                <p class="text-[10px] font-extrabold uppercase
                          tracking-[0.2em] text-slate-400">
                    Examination History
                </p>

                <h2 class="mt-1 text-lg font-extrabold text-slate-950">
                    Recent examinations
                </h2>

            </div>


            <span class="text-xs font-bold text-slate-400">
                {{ $attempts->count() }}
                {{ \Illuminate\Support\Str::plural(
                    'exam',
                    $attempts->count()
                ) }}
            </span>

        </div>


        <div class="border-t border-slate-200">

            @forelse($attempts as $attempt)

            @php

            $passingScore =
            $attempt->exam?->passing_score ?? 0;

            $passed =
            ($attempt->score ?? 0) >= $passingScore;

            $attemptPercentage =
            ($attempt->total_score ?? 0) > 0
            ? (($attempt->score ?? 0) /
            $attempt->total_score) * 100
            : null;

            @endphp


            <div
                class="group grid gap-4 border-b
                           border-slate-200 py-5
                           transition-colors
                           hover:bg-white
                           sm:grid-cols-[minmax(0,1fr)_120px_120px]
                           sm:items-center">


                <div>

                    <div class="flex flex-wrap items-center gap-2">

                        <h3 class="text-sm font-extrabold text-slate-850">
                            {{ $attempt->exam?->title ?? 'Exam unavailable' }}
                        </h3>


                        <span
                            class="inline-flex rounded-full px-2 py-0.5
                                       text-[8px] font-extrabold uppercase
                                       tracking-wider
                                       {{ $passed
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : 'bg-rose-50 text-rose-700' }}">

                            {{ $passed ? 'Passed' : 'Failed' }}

                        </span>

                    </div>


                    <p class="mt-1 text-xs text-slate-400">

                        {{ $attempt->exam?->subject?->name ?? 'No subject' }}

                        <span class="mx-1 text-slate-300">
                            ·
                        </span>

                        {{ $attempt->submitted_at
                                ? \Carbon\Carbon::parse(
                                    $attempt->submitted_at
                                  )->format('M d, Y')
                                : 'Date unavailable' }}

                    </p>

                </div>



                <div>

                    <p class="text-[9px] font-extrabold uppercase
                                  tracking-[0.15em] text-slate-400">
                        Score
                    </p>

                    <p class="mt-1 text-xs font-black text-slate-750">
                        {{ $attempt->score ?? 0 }}
                        /
                        {{ $attempt->total_score ?? '—' }}
                    </p>

                </div>



                <div class="sm:text-right">

                    @if($attemptPercentage !== null)

                    <p class="text-lg font-black
                                      {{ $passed
                                            ? 'text-emerald-600'
                                            : 'text-rose-600' }}">
                        {{ number_format($attemptPercentage, 1) }}%
                    </p>

                    @else

                    <p class="text-lg font-black text-slate-400">
                        —
                    </p>

                    @endif

                </div>

            </div>

            @empty

            <div class="border-b border-slate-200 py-14 text-center">

                <p class="text-sm font-bold text-slate-600">
                    No examinations completed yet.
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Enter an exam code to take your first examination.
                </p>


                <button
                    type="button"
                    @click="examPanel = true;
                                window.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                })"
                    class="mt-4 text-xs font-bold text-indigo-600
                               transition-colors hover:text-indigo-800">
                    Enter Exam Code →
                </button>

            </div>

            @endforelse

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- LEARNING RECOMMENDATION                                --}}
    {{-- ====================================================== --}}

    <section class="border border-slate-200 bg-white">

        <div class="grid lg:grid-cols-[240px_1fr]">


            <div class="border-b border-slate-200
                        p-6 lg:border-b-0 lg:border-r">

                <p class="text-[10px] font-extrabold uppercase
                          tracking-[0.2em] text-indigo-600">
                    Learning Recommendation
                </p>

                <h2 class="mt-2 text-lg font-extrabold text-slate-950">
                    What to review next
                </h2>

                <p class="mt-2 text-xs leading-5 text-slate-400">
                    Generated from your examination performance data.
                </p>

            </div>


            <div class="p-6">

                @if($latestRecommendation)

                @if($latestRecommendation->subject)

                <p class="mb-3 text-[9px] font-extrabold uppercase
                                  tracking-[0.16em] text-slate-400">

                    {{ $latestRecommendation->subject->name }}

                </p>

                @endif


                <p class="max-w-3xl text-sm font-medium
                              leading-7 text-slate-700">

                    {{ $latestRecommendation->recommendation_text }}

                </p>

                @else

                <p class="text-sm font-medium text-slate-500">
                    Complete an examination to receive a personalized
                    learning recommendation.
                </p>

                @endif

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- AI-Q FUTURE SIGNAL                                     --}}
    {{-- ====================================================== --}}

    <section class="border-t border-slate-200 pt-7">

        <div class="flex flex-col gap-4
                    sm:flex-row sm:items-center
                    sm:justify-between">

            <div>

                <div class="flex items-center gap-2">

                    <span class="flex h-6 w-6 items-center
                                 justify-center rounded-md
                                 bg-indigo-50 text-xs
                                 font-black text-indigo-600">
                        ◆
                    </span>

                    <p class="text-[10px] font-extrabold uppercase
                              tracking-[0.2em] text-indigo-600">
                        AI-Q Insights
                    </p>

                </div>


                <p class="mt-2 max-w-xl text-sm text-slate-500">
                    AI-assisted interpretation and personalized academic
                    insights will appear here after Gemini integration.
                </p>

            </div>


            <span
                class="w-fit rounded-full bg-slate-100
                       px-3 py-1.5 text-[9px]
                       font-extrabold uppercase
                       tracking-[0.15em] text-slate-400">

                Gemini Integration Next

            </span>

        </div>

    </section>

</div>

@endsection