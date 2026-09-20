@extends('layouts.app')

@section('title', 'Command Center')
@section('eyebrow', 'Teacher Workspace')

@section('page-actions')
<a
    href="{{ url('/teacher/exams') }}"
    class="group inline-flex items-center gap-2
               rounded-xl bg-slate-950 px-4 py-2.5
               text-xs font-bold text-white
               transition-all duration-200
               hover:-translate-y-0.5
               hover:bg-indigo-600
               hover:shadow-lg hover:shadow-indigo-200
               active:translate-y-0 active:scale-95">
    <svg
        class="h-4 w-4 transition-transform duration-300
                   group-hover:rotate-90 group-hover:scale-110"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24">
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2.3"
            d="M12 4v16m8-8H4" />
    </svg>

    Create Exam
</a>
@endsection


@section('content')

<div class="space-y-10">

    {{-- ====================================================== --}}
    {{-- ACADEMIC PULSE                                         --}}
    {{-- ====================================================== --}}

    <section>

        <div class="mb-6 flex flex-col gap-2
                    sm:flex-row sm:items-end sm:justify-between">

            <div>
                <p class="text-[10px] font-extrabold uppercase
                          tracking-[0.22em] text-indigo-600">
                    Academic Pulse
                </p>

                <h2 class="mt-1 text-2xl font-extrabold
                           tracking-[-0.04em] text-slate-950">
                    Good day, {{ auth()->user()->fname }}.
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Here's what's happening across your examinations.
                </p>
            </div>

            <p class="text-xs font-semibold text-slate-400">
                {{ now()->format('F d, Y') }}
            </p>

        </div>


        {{-- Main metrics --}}
        <div class="grid grid-cols-2 gap-x-6 gap-y-6
                    border-y border-slate-200 py-6
                    lg:grid-cols-4">

            {{-- Exams --}}
            <a
                href="{{ url('/teacher/exams') }}"
                class="group relative border-r border-slate-200
                       pr-5 transition-all duration-300
                       active:scale-[0.97]">

                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-[10px] font-extrabold uppercase
                                  tracking-[0.18em] text-slate-400">
                            Examinations
                        </p>

                        <p class="mt-2 text-3xl font-black
                                  tracking-[-0.05em] text-slate-950
                                  transition-colors
                                  group-hover:text-indigo-600">
                            {{ $totalExams ?? 0 }}
                        </p>
                    </div>

                    <span
                        class="flex h-8 w-8 items-center justify-center
                               rounded-full bg-slate-100
                               text-sm text-slate-500
                               transition-all duration-300
                               group-hover:translate-x-1
                               group-hover:-translate-y-1
                               group-hover:bg-indigo-600
                               group-hover:text-white">
                        ↗
                    </span>

                </div>

                <p class="mt-2 text-xs font-medium text-slate-400">
                    Created assessments
                </p>

                <div
                    class="absolute -bottom-6 left-0 h-[2px] w-0
                           bg-indigo-600 transition-all duration-300
                           group-hover:w-full"></div>

            </a>


            {{-- Questions --}}
            <a
                href="{{ url('/teacher/exams') }}"
                class="group relative lg:border-r
                       lg:border-slate-200 lg:pr-5
                       transition-all duration-300
                       active:scale-[0.97]">

                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-[10px] font-extrabold uppercase
                                  tracking-[0.18em] text-slate-400">
                            Question Bank
                        </p>

                        <p class="mt-2 text-3xl font-black
                                  tracking-[-0.05em] text-slate-950
                                  transition-colors
                                  group-hover:text-indigo-600">
                            {{ $totalQuestions ?? 0 }}
                        </p>
                    </div>

                    <span
                        class="flex h-8 w-8 items-center justify-center
                               rounded-full bg-slate-100
                               text-sm text-slate-500
                               transition-all duration-300
                               group-hover:rotate-6
                               group-hover:bg-indigo-600
                               group-hover:text-white">
                        ↗
                    </span>

                </div>

                <p class="mt-2 text-xs font-medium text-slate-400">
                    Items across all exams
                </p>

                <div
                    class="absolute -bottom-6 left-0 h-[2px] w-0
                           bg-indigo-600 transition-all duration-300
                           group-hover:w-full"></div>

            </a>


            {{-- Attempts --}}
            <a
                href="{{ route('teacher.analytics.index') }}"
                class="group relative border-r border-slate-200
                       pr-5 transition-all duration-300
                       active:scale-[0.97]">

                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-[10px] font-extrabold uppercase
                                  tracking-[0.18em] text-slate-400">
                            Responses
                        </p>

                        <p class="mt-2 text-3xl font-black
                                  tracking-[-0.05em] text-slate-950
                                  transition-colors
                                  group-hover:text-indigo-600">
                            {{ $totalAttempts ?? 0 }}
                        </p>
                    </div>

                    <span
                        class="flex h-8 w-8 items-center justify-center
                               rounded-full bg-slate-100
                               text-sm text-slate-500
                               transition-all duration-300
                               group-hover:scale-110
                               group-hover:bg-indigo-600
                               group-hover:text-white">
                        ↗
                    </span>

                </div>

                <p class="mt-2 text-xs font-medium text-slate-400">
                    Student submissions
                </p>

                <div
                    class="absolute -bottom-6 left-0 h-[2px] w-0
                           bg-indigo-600 transition-all duration-300
                           group-hover:w-full"></div>

            </a>


            {{-- Performance --}}
            <a
                href="{{ route('teacher.analytics.index') }}"
                class="group relative
                       transition-all duration-300
                       active:scale-[0.97]">

                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-[10px] font-extrabold uppercase
                                  tracking-[0.18em] text-slate-400">
                            Performance
                        </p>

                        <p class="mt-2 text-3xl font-black
                                  tracking-[-0.05em] text-slate-950
                                  transition-colors
                                  group-hover:text-indigo-600">
                            {{ isset($averageScore)
                                ? number_format($averageScore, 1) . '%'
                                : '0%' }}
                        </p>
                    </div>

                    <span
                        class="flex h-8 w-8 items-center justify-center
                               rounded-full bg-indigo-50
                               text-sm text-indigo-600
                               transition-all duration-300
                               group-hover:translate-x-1
                               group-hover:-translate-y-1
                               group-hover:bg-indigo-600
                               group-hover:text-white">
                        ↗
                    </span>

                </div>

                <p class="mt-2 text-xs font-medium text-slate-400">
                    Overall examination average
                </p>

                <div
                    class="absolute -bottom-6 left-0 h-[2px] w-0
                           bg-indigo-600 transition-all duration-300
                           group-hover:w-full"></div>

            </a>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- COMMAND WORKSPACE                                      --}}
    {{-- ====================================================== --}}

    <section class="grid grid-cols-1 gap-6 lg:grid-cols-12">

        {{-- Performance Workspace --}}
        <a
            href="{{ route('teacher.analytics.index') }}"
            class="group relative overflow-hidden
                   border border-slate-200 bg-white
                   lg:col-span-8
                   transition-all duration-300
                   hover:-translate-y-1
                   hover:border-indigo-200
                   hover:shadow-xl hover:shadow-slate-200/70
                   active:translate-y-0 active:scale-[0.99]">

            <div class="p-6 sm:p-8">

                <div class="flex items-start justify-between gap-5">

                    <div>

                        <div class="flex items-center gap-2">

                            <span
                                class="h-2 w-2 rounded-full
                                       bg-indigo-600
                                       transition-transform duration-300
                                       group-hover:scale-150"></span>

                            <p class="text-[10px] font-extrabold
                                      uppercase tracking-[0.2em]
                                      text-indigo-600">
                                Performance
                            </p>

                        </div>

                        <h3 class="mt-3 text-xl font-extrabold
                                   tracking-[-0.035em]
                                   text-slate-950">
                            Examination Analytics
                        </h3>

                        <p class="mt-2 max-w-xl text-sm
                                  leading-6 text-slate-500">
                            Explore student results, score distribution,
                            performance trends and examination outcomes.
                        </p>

                    </div>


                    <div
                        class="flex h-10 w-10 shrink-0
                               items-center justify-center
                               rounded-full border border-slate-200
                               text-slate-500
                               transition-all duration-300
                               group-hover:rotate-6
                               group-hover:border-indigo-600
                               group-hover:bg-indigo-600
                               group-hover:text-white">
                        ↗
                    </div>

                </div>


                {{-- Visual performance area --}}
                <div class="mt-10">

                    <div class="flex items-end justify-between">

                        <div>

                            <p class="text-5xl font-black
                                      tracking-[-0.07em]
                                      text-slate-950">
                                {{ isset($averageScore)
                                    ? number_format($averageScore, 1)
                                    : '0.0' }}
                                <span class="text-xl text-slate-300">%</span>
                            </p>

                            <p class="mt-2 text-xs font-semibold
                                      text-slate-400">
                                Overall average
                            </p>

                        </div>

                        <span
                            class="hidden text-xs font-bold
                                   text-indigo-600
                                   transition-transform duration-300
                                   group-hover:translate-x-1
                                   sm:block">
                            Explore analytics →
                        </span>

                    </div>


                    {{-- Performance bar --}}
                    @php
                    $dashboardAverage = max(
                    0,
                    min(100, (float) ($averageScore ?? 0))
                    );
                    @endphp

                    <div
                        class="mt-7 h-2 overflow-hidden
                               bg-slate-100">

                        <div
                            class="h-full bg-indigo-600
                                   transition-all duration-1000
                                   ease-out
                                   group-hover:bg-indigo-500"
                            style="width: {{ $dashboardAverage }}%"></div>

                    </div>


                    <div
                        class="mt-3 flex justify-between
                               text-[10px] font-bold
                               uppercase tracking-wider
                               text-slate-300">
                        <span>0</span>
                        <span>25</span>
                        <span>50</span>
                        <span>75</span>
                        <span>100</span>
                    </div>

                </div>

            </div>

        </a>



        {{-- Quick Actions --}}
        <div
            class="border border-slate-200 bg-slate-950
                   p-6 text-white lg:col-span-4 sm:p-8">

            <p class="text-[10px] font-extrabold uppercase
                      tracking-[0.2em] text-indigo-400">
                Quick Actions
            </p>

            <h3 class="mt-2 text-xl font-extrabold
                       tracking-[-0.035em]">
                Continue your work.
            </h3>


            <div class="mt-7 space-y-2">

                <a
                    href="{{ url('/teacher/exams') }}"
                    class="group flex items-center
                           justify-between
                           border-b border-slate-800
                           py-4
                           transition-all duration-200
                           hover:pl-2
                           active:scale-[0.98]">

                    <div>

                        <p class="text-sm font-bold">
                            Manage examinations
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Create and edit exams
                        </p>

                    </div>

                    <span
                        class="text-slate-500
                               transition-all duration-300
                               group-hover:translate-x-1
                               group-hover:text-indigo-400">
                        →
                    </span>

                </a>


                <a
                    href="{{ route('teacher.class-subjects.index') }}"
                    class="group flex items-center
                           justify-between
                           border-b border-slate-800
                           py-4
                           transition-all duration-200
                           hover:pl-2
                           active:scale-[0.98]">

                    <div>

                        <p class="text-sm font-bold">
                            Classes & subjects
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Organize academic records
                        </p>

                    </div>

                    <span
                        class="text-slate-500
                               transition-all duration-300
                               group-hover:translate-x-1
                               group-hover:text-indigo-400">
                        →
                    </span>

                </a>


                <a
                    href="{{ route('teacher.analytics.index') }}"
                    class="group flex items-center
                           justify-between
                           py-4
                           transition-all duration-200
                           hover:pl-2
                           active:scale-[0.98]">

                    <div>

                        <p class="text-sm font-bold">
                            Review analytics
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Analyze student performance
                        </p>

                    </div>

                    <span
                        class="text-slate-500
                               transition-all duration-300
                               group-hover:translate-x-1
                               group-hover:text-indigo-400">
                        →
                    </span>

                </a>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- ACTIVITY / SIGNAL AREA                                  --}}
    {{-- ====================================================== --}}

    <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Examination Activity --}}
        <a
            href="{{ url('/teacher/exams') }}"
            class="group border-t border-slate-300
                   py-6 transition-all duration-300">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-[10px] font-extrabold uppercase
                              tracking-[0.2em] text-slate-400">
                        Examination Activity
                    </p>

                    <h3 class="mt-2 text-lg font-extrabold
                               tracking-tight text-slate-950">
                        {{ $totalExams ?? 0 }}
                        examinations created
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Manage questions, publishing status,
                        exam codes and student access.
                    </p>

                </div>

                <span
                    class="mt-1 text-xl text-slate-300
                           transition-all duration-300
                           group-hover:translate-x-2
                           group-hover:text-indigo-600">
                    →
                </span>

            </div>

        </a>


        {{-- Student Activity --}}
        <a
            href="{{ route('teacher.analytics.index') }}"
            class="group border-t border-slate-300
                   py-6 transition-all duration-300">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-[10px] font-extrabold uppercase
                              tracking-[0.2em] text-slate-400">
                        Student Activity
                    </p>

                    <h3 class="mt-2 text-lg font-extrabold
                               tracking-tight text-slate-950">
                        {{ $totalAttempts ?? 0 }}
                        recorded responses
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Review completed attempts and
                        examination performance.
                    </p>

                </div>

                <span
                    class="mt-1 text-xl text-slate-300
                           transition-all duration-300
                           group-hover:translate-x-2
                           group-hover:text-indigo-600">
                    →
                </span>

            </div>

        </a>

    </section>



    {{-- ====================================================== --}}
    {{-- AI-Q PLACEHOLDER                                       --}}
    {{-- ====================================================== --}}

    <section
        class="relative overflow-hidden
               border border-indigo-100
               bg-indigo-50/60 p-6 sm:p-8">

        <div
            class="absolute -right-20 -top-20
                   h-52 w-52 rounded-full
                   bg-indigo-100/60 blur-3xl"></div>


        <div
            class="relative flex flex-col gap-5
                   sm:flex-row sm:items-center
                   sm:justify-between">

            <div class="max-w-2xl">

                <div class="flex items-center gap-2">

                    <span
                        class="relative flex h-2.5 w-2.5">
                        <span
                            class="absolute inline-flex h-full w-full
                                   animate-ping rounded-full
                                   bg-indigo-400 opacity-40"></span>

                        <span
                            class="relative inline-flex h-2.5 w-2.5
                                   rounded-full bg-indigo-600"></span>
                    </span>

                    <p class="text-[10px] font-extrabold
                              uppercase tracking-[0.22em]
                              text-indigo-600">
                        AI-Q Intelligence
                    </p>

                </div>

                <h3
                    class="mt-3 text-xl font-extrabold
                           tracking-[-0.035em]
                           text-slate-950">
                    AI-assisted insights are coming next.
                </h3>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    AI-Q will use your existing examination analytics
                    to explain performance patterns and generate
                    personalized academic recommendations.
                </p>

            </div>


            <div
                class="shrink-0 rounded-full
                       border border-indigo-200
                       bg-white/70 px-4 py-2
                       text-[10px] font-extrabold
                       uppercase tracking-[0.15em]
                       text-indigo-500">
                Gemini Integration Next
            </div>

        </div>

    </section>

</div>

@endsection