@extends('layouts.app')

@section('title', 'Exam Result')
@section('eyebrow', 'Examination Result')

@section('content')

@php

    $score = (float) ($attempt->score ?? 0);
    $totalScore = (float) ($attempt->total_score ?? 0);

    $percentage = $totalScore > 0
        ? ($score / $totalScore) * 100
        : 0;

    /*
     * Preserve the existing backend rule:
     * passing_score is compared against the raw earned score.
     */
    $passingScore = (float) ($attempt->exam->passing_score ?? 0);

    $hasPassed = $score >= $passingScore;

    $safePercentage = max(0, min(100, $percentage));

@endphp


<div
    x-data="{ visible: false }"
    x-init="setTimeout(() => visible = true, 100)"
    class="mx-auto max-w-5xl">


    {{-- ====================================================== --}}
    {{-- RESULT IDENTITY                                        --}}
    {{-- ====================================================== --}}

    <section
        x-show="visible"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-y-3"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="border-b border-slate-200 pb-8">


        <div class="flex flex-col gap-6
                    lg:flex-row lg:items-end lg:justify-between">


            <div>

                <div class="flex items-center gap-2">

                    <span
                        class="h-2 w-2 rounded-full
                               {{ $hasPassed
                                    ? 'bg-emerald-500'
                                    : 'bg-rose-500' }}">
                    </span>

                    <p
                        class="text-[10px] font-extrabold uppercase
                               tracking-[0.2em]
                               {{ $hasPassed
                                    ? 'text-emerald-600'
                                    : 'text-rose-600' }}">

                        Examination Completed

                    </p>

                </div>


                <h1
                    class="mt-3 text-3xl font-black
                           tracking-[-0.055em] text-slate-950
                           sm:text-4xl">

                    {{ $attempt->exam->title }}

                </h1>


                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">

                    Your examination has been submitted and
                    automatically scored by AI-Q.

                </p>

            </div>



            <div class="text-left lg:text-right">

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.18em] text-slate-400">
                    Result
                </p>


                @if($hasPassed)

                    <p class="mt-1 text-sm font-black
                              uppercase tracking-wider
                              text-emerald-600">
                        Passed
                    </p>

                @else

                    <p class="mt-1 text-sm font-black
                              uppercase tracking-wider
                              text-rose-600">
                        Needs Improvement
                    </p>

                @endif

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- SCORE HERO                                             --}}
    {{-- ====================================================== --}}

    <section
        x-show="visible"
        x-transition:enter="transition ease-out duration-500 delay-100"
        x-transition:enter-start="opacity-0 translate-y-3"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="py-9">


        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_340px]">


            {{-- Main score --}}
            <div>

                <p class="text-[10px] font-extrabold uppercase
                          tracking-[0.2em] text-indigo-600">
                    Examination Performance
                </p>


                <div class="mt-5 flex flex-wrap
                            items-end gap-x-8 gap-y-5">


                    <div>

                        <p class="text-[9px] font-extrabold uppercase
                                  tracking-[0.16em] text-slate-400">
                            Your Score
                        </p>


                        <div class="mt-1 flex items-baseline gap-2">

                            <span
                                class="text-6xl font-black
                                       tracking-[-0.075em]
                                       text-slate-950 sm:text-7xl">

                                {{ number_format($score, 0) }}

                            </span>


                            <span class="text-xl font-bold text-slate-300">

                                / {{ number_format($totalScore, 0) }}

                            </span>

                        </div>

                    </div>



                    <div class="border-l border-slate-200 pl-7">

                        <p class="text-[9px] font-extrabold uppercase
                                  tracking-[0.16em] text-slate-400">
                            Percentage
                        </p>

                        <p class="mt-2 text-3xl font-black
                                  tracking-[-0.05em]
                                  text-indigo-600">

                            {{ number_format($percentage, 1) }}%

                        </p>

                    </div>

                </div>



                {{-- Score line --}}
                <div class="mt-8 max-w-2xl">

                    <div class="h-2 overflow-hidden bg-slate-100">

                        <div
                            class="h-full transition-all
                                   duration-1000 ease-out
                                   {{ $hasPassed
                                        ? 'bg-emerald-500'
                                        : 'bg-rose-500' }}"
                            style="width: {{ $safePercentage }}%">
                        </div>

                    </div>


                    <div class="mt-2 flex justify-between
                                text-[9px] font-bold text-slate-300">

                        <span>0%</span>
                        <span>25%</span>
                        <span>50%</span>
                        <span>75%</span>
                        <span>100%</span>

                    </div>

                </div>

            </div>



            {{-- Status panel --}}
            <div
                class="p-6 text-white
                       {{ $hasPassed
                            ? 'bg-slate-950'
                            : 'bg-slate-950' }}">


                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.18em]
                          {{ $hasPassed
                                ? 'text-emerald-400'
                                : 'text-rose-400' }}">

                    Performance Status

                </p>



                @if($hasPassed)

                    <div
                        class="mt-5 flex h-10 w-10
                               items-center justify-center
                               rounded-full bg-emerald-400/10
                               text-lg font-black
                               text-emerald-400">

                        ✓

                    </div>


                    <h2 class="mt-4 text-2xl font-black
                               tracking-tight">
                        Passed
                    </h2>


                    <p class="mt-2 text-xs leading-5 text-slate-400">
                        Your earned score met or exceeded the
                        required passing score for this examination.
                    </p>

                @else

                    <div
                        class="mt-5 flex h-10 w-10
                               items-center justify-center
                               rounded-full bg-rose-400/10
                               text-lg font-black
                               text-rose-400">

                        !

                    </div>


                    <h2 class="mt-4 text-2xl font-black
                               tracking-tight">
                        Needs Improvement
                    </h2>


                    <p class="mt-2 text-xs leading-5 text-slate-400">
                        Your earned score is below the required
                        passing score for this examination.
                    </p>

                @endif



                <div class="mt-6 border-t border-slate-800 pt-5">

                    <div class="flex items-center justify-between">

                        <span class="text-[10px] font-semibold
                                     text-slate-500">
                            Required score
                        </span>

                        <span class="text-sm font-black text-white">

                            {{ number_format($passingScore, 0) }}
                            pts

                        </span>

                    </div>


                    <div class="mt-3 flex items-center justify-between">

                        <span class="text-[10px] font-semibold
                                     text-slate-500">
                            Earned score
                        </span>

                        <span
                            class="text-sm font-black
                                   {{ $hasPassed
                                        ? 'text-emerald-400'
                                        : 'text-rose-400' }}">

                            {{ number_format($score, 0) }}
                            pts

                        </span>

                    </div>

                </div>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- RESULT BREAKDOWN                                       --}}
    {{-- ====================================================== --}}

    <section class="border-y border-slate-200 py-7">

        <p class="text-[10px] font-extrabold uppercase
                  tracking-[0.2em] text-slate-400">
            Result Summary
        </p>


        <div class="mt-5 grid grid-cols-2 gap-x-6 gap-y-6
                    sm:grid-cols-4">


            {{-- Earned --}}
            <div class="border-r border-slate-200 pr-4">

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.15em] text-slate-400">
                    Earned
                </p>

                <p class="mt-2 text-2xl font-black
                          tracking-[-0.04em] text-slate-950">
                    {{ number_format($score, 0) }}
                </p>

                <p class="mt-1 text-[10px] text-slate-400">
                    Points
                </p>

            </div>



            {{-- Total --}}
            <div class="border-r border-slate-200 pr-4">

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.15em] text-slate-400">
                    Total
                </p>

                <p class="mt-2 text-2xl font-black
                          tracking-[-0.04em] text-slate-950">
                    {{ number_format($totalScore, 0) }}
                </p>

                <p class="mt-1 text-[10px] text-slate-400">
                    Possible points
                </p>

            </div>



            {{-- Percentage --}}
            <div class="border-r border-slate-200 pr-4">

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.15em] text-slate-400">
                    Percentage
                </p>

                <p class="mt-2 text-2xl font-black
                          tracking-[-0.04em] text-indigo-600">
                    {{ number_format($percentage, 1) }}%
                </p>

                <p class="mt-1 text-[10px] text-slate-400">
                    Performance
                </p>

            </div>



            {{-- Status --}}
            <div>

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.15em] text-slate-400">
                    Status
                </p>

                <p
                    class="mt-2 text-sm font-black uppercase
                           tracking-wide
                           {{ $hasPassed
                                ? 'text-emerald-600'
                                : 'text-rose-600' }}">

                    {{ $hasPassed ? 'Passed' : 'Review' }}

                </p>

                <p class="mt-1 text-[10px] text-slate-400">
                    Assessment result
                </p>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- WHAT HAPPENS NEXT                                      --}}
    {{-- ====================================================== --}}

    <section class="py-8">

        <div class="grid gap-6 lg:grid-cols-2">


            {{-- Analytics --}}
            <div class="border-t-2 border-indigo-500 bg-white p-5">

                <div class="flex items-start gap-4">

                    <span
                        class="flex h-8 w-8 shrink-0
                               items-center justify-center
                               rounded-lg bg-indigo-50
                               text-xs font-black
                               text-indigo-600">
                        01
                    </span>


                    <div>

                        <p class="text-[9px] font-extrabold uppercase
                                  tracking-[0.16em] text-indigo-600">
                            Performance Updated
                        </p>

                        <h3 class="mt-1 text-sm font-extrabold
                                   text-slate-900">
                            Your analytics have been updated
                        </h3>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            This result contributes to your overall
                            average, subject performance, improvement
                            tracking, and topic analysis.
                        </p>

                    </div>

                </div>

            </div>



            {{-- Recommendation --}}
            <div class="border-t-2 border-slate-800 bg-white p-5">

                <div class="flex items-start gap-4">

                    <span
                        class="flex h-8 w-8 shrink-0
                               items-center justify-center
                               rounded-lg bg-slate-100
                               text-xs font-black
                               text-slate-700">
                        02
                    </span>


                    <div>

                        <p class="text-[9px] font-extrabold uppercase
                                  tracking-[0.16em] text-slate-500">
                            Learning Recommendation
                        </p>

                        <h3 class="mt-1 text-sm font-extrabold
                                   text-slate-900">
                            Review your academic focus
                        </h3>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Return to your dashboard to review your
                            latest performance signals and learning
                            recommendation.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- ACTIONS                                                --}}
    {{-- ====================================================== --}}

    <section
        class="flex flex-col gap-4 border-t
               border-slate-200 pt-7
               sm:flex-row sm:items-center
               sm:justify-between">


        <div>

            <p class="text-sm font-bold text-slate-800">
                Examination complete.
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Your result has been saved to your examination history.
            </p>

        </div>


        <a
            href="{{ route('student.dashboard') }}"
            class="group inline-flex items-center
                   justify-center gap-2 rounded-xl
                   bg-indigo-600 px-5 py-3
                   text-xs font-bold text-white
                   transition-all duration-200
                   hover:-translate-y-0.5
                   hover:bg-indigo-500
                   hover:shadow-lg
                   hover:shadow-indigo-500/20
                   active:translate-y-0
                   active:scale-95">

            View Performance

            <span
                class="transition-transform
                       group-hover:translate-x-1">
                →
            </span>

        </a>

    </section>



    {{-- ====================================================== --}}
    {{-- AI-Q FUTURE                                            --}}
    {{-- ====================================================== --}}

    <section class="mt-8 border-t border-slate-200 pt-6">

        <div class="flex items-start gap-3">

            <span
                class="flex h-7 w-7 shrink-0
                       items-center justify-center
                       rounded-md bg-indigo-50
                       text-[10px] font-black
                       text-indigo-600">
                ◆
            </span>


            <div>

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.18em] text-indigo-600">
                    AI-Q Insights
                </p>

                <p class="mt-1 max-w-2xl text-xs
                          leading-5 text-slate-400">
                    AI-assisted interpretation of your examination
                    performance will become available after the
                    Gemini integration is connected.
                </p>

            </div>

        </div>

    </section>

</div>

@endsection