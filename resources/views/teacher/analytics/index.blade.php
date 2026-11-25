@extends('layouts.app')

@section('title', 'Performance Analytics')
@section('eyebrow', 'Analytics Workspace')

@section('content')

<style>
    .aiq-analytics-page { --aiq-purple:#7047df; --aiq-ink:#292437; --aiq-pink:#f4a8e9; --aiq-yellow:#ffe16a; color:var(--aiq-ink); }
    .analytics-hero { border:1.5px solid #33253f !important; border-radius:14px; padding:22px; background:linear-gradient(120deg,#ffe16a,#fff1b5 55%,#f2eaff); box-shadow:5px 6px 0 #33253f; }
    .analytics-hero h1 { color:#292437 !important; }
    .analytics-tabs { border-color:#ddd4e8 !important; }
    .analytics-tabs button,.analytics-tabs a { transition:transform .18s,color .18s; }
    .analytics-tabs button:hover,.analytics-tabs a:hover { transform:translateY(-2px); color:#7047df !important; }
    .analytics-tabs [x-show] { background:#ffe16a !important; }
    .analytics-tabs .rounded-full { background:#f1eaff !important; color:#5937bd; }
    .aiq-analytics-page .border.border-slate-200.bg-white { border:1.5px solid #382d43 !important; border-radius:12px; background:#fffdf8 !important; box-shadow:0 4px 0 #382d43; transition:transform .2s,box-shadow .2s; }
    .aiq-analytics-page .border.border-slate-200.bg-white:hover { transform:translateY(-3px); box-shadow:0 7px 0 #382d43; }
    .aiq-analytics-page [data-aiq-tilt] { --tilt-x:0deg; --tilt-y:0deg; transform:perspective(1000px) rotateX(var(--tilt-x)) rotateY(var(--tilt-y)); transform-style:preserve-3d; transition:transform .16s ease,box-shadow .2s; }
    .analytics-metric-grid > div { --tilt-x:0deg; --tilt-y:0deg; min-height:112px; padding:14px; border:1.5px solid #382d43; border-radius:10px; background:#fff; box-shadow:0 4px 0 #382d43; }
    .analytics-metric-grid > div:nth-child(2) { background:#fff2bd; }
    .analytics-metric-grid > div:nth-child(3) { background:#f9e4f6; }
    .analytics-metric-grid > div:nth-child(4) { background:#e7f3e5; }
    .analytics-metric-grid > div:hover { transform:perspective(1000px) rotateX(var(--tilt-x)) rotateY(var(--tilt-y)) translateY(-4px); box-shadow:0 8px 0 #382d43; }
    .aiq-analytics-page [class~="bg-slate-950"] { border:1.5px solid #30243e; border-radius:13px; background:linear-gradient(145deg,#7047df,#5230b5) !important; box-shadow:5px 6px 0 #30243e; }
    .aiq-analytics-page [class~="bg-slate-950"] h2,.aiq-analytics-page [class~="bg-slate-950"] h3 { color:#fff !important; }
    .aiq-analytics-page [class~="bg-slate-950"] .text-indigo-400 { color:#ffe16a !important; }
    .aiq-analytics-page [class~="bg-slate-950"] .text-slate-500,.aiq-analytics-page [class~="bg-slate-950"] .text-slate-400 { color:#e1d8ff !important; }
    .aiq-analytics-page table thead { background:#f2eaff !important; color:#5937bd !important; }
    .aiq-analytics-page table tbody tr { transition:background .18s; }
    .aiq-analytics-page table tbody tr:hover { background:#fff8dd; }
    .aiq-analytics-page input { border-radius:9px; }
    @media(max-width:640px){.analytics-hero{padding:16px}.analytics-metric-grid > div{min-height:98px;padding:10px}}
    @media(prefers-reduced-motion:reduce){.aiq-analytics-page *,.aiq-analytics-page *::before,.aiq-analytics-page *::after{transition-duration:.01ms!important;animation-duration:.01ms!important}}
</style>
@php
$totalStudents = collect($studentRankings)->count();

$improvingCount = collect($improvingStudents)->count();
$decliningCount = collect($decliningStudents)->count();

$safeAverage = max(0, min(100, (float) $averageScore));
$safePassRate = max(0, min(100, (float) $passRate));
@endphp


<div
    x-data="{
        section: 'performance',
        studentSearch: ''
    }"
    class="aiq-analytics-page space-y-8">


    {{-- ====================================================== --}}
    {{-- HEADER                                                 --}}
    {{-- ====================================================== --}}

    <section class="analytics-hero border-b border-slate-200 pb-8">


        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">

            <div>

                <p class="text-[10px] font-extrabold uppercase tracking-[0.22em] text-indigo-600">
                    Academic Performance
                </p>

                <h1
                    class="mt-2 text-3xl font-black tracking-[-0.055em]
                           text-slate-950 sm:text-4xl">
                    Performance Analytics
                </h1>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                    Monitor examination outcomes, student performance,
                    score distribution and changes in academic performance.
                </p>

            </div>


            <div class="flex items-center gap-5">

                <div class="text-right">

                    <p
                        class="text-[9px] font-extrabold uppercase
                               tracking-[0.18em] text-slate-400">
                        Completed Attempts
                    </p>

                    <p class="mt-1 text-xl font-black text-slate-950">
                        {{ $totalAttempts }}
                    </p>

                </div>

                <div class="h-9 w-px bg-slate-200"></div>

                <div class="text-right">

                    <p
                        class="text-[9px] font-extrabold uppercase
                               tracking-[0.18em] text-slate-400">
                        Students
                    </p>

                    <p class="mt-1 text-xl font-black text-slate-950">
                        {{ $totalStudents }}
                    </p>

                </div>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- ANALYTICS NAVIGATION                                   --}}
    {{-- ====================================================== --}}

    <nav class="analytics-tabs flex items-center gap-1 overflow-x-auto border-b border-slate-200">

        <button
            type="button"
            @click="section = 'performance'"
            :class="section === 'performance'
                ? 'text-indigo-700'
                : 'text-slate-500 hover:text-slate-950'"
            class="relative shrink-0 px-4 py-4 text-xs font-bold
                   transition-all active:scale-95 aiq-button aiq-button-secondary">

            Performance

            <span
                x-show="section === 'performance'"
                class="absolute inset-x-3 bottom-0 h-[2px] bg-indigo-600">
            </span>

        </button>


        <button
            type="button"
            @click="section = 'students'"
            :class="section === 'students'
                ? 'text-indigo-700'
                : 'text-slate-500 hover:text-slate-950'"
            class="relative shrink-0 px-4 py-4 text-xs font-bold
                   transition-all active:scale-95 aiq-button aiq-button-secondary">

            Students

            <span
                class="ml-1 rounded-full bg-slate-100
                       px-2 py-0.5 text-[9px]">
                {{ $totalStudents }}
            </span>

            <span
                x-show="section === 'students'"
                class="absolute inset-x-3 bottom-0 h-[2px] bg-indigo-600">
            </span>

        </button>


        @if(Route::has('teacher.analytics.predictions'))

        <a
            href="{{ route('teacher.analytics.predictions') }}"
            class="group relative shrink-0 px-4 py-4
                       text-xs font-bold text-slate-500
                       transition-all hover:text-indigo-600
                       active:scale-95 aiq-button aiq-button-secondary">

            Predictions

            <span
                class="ml-1 inline-block transition-transform
                           group-hover:translate-x-1">
                ↗
            </span>

        </a>
        <a
            href="{{ route('teacher.analytics.export') }}"
            class="aiq-button aiq-button-primary rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
            Export CSV
        </a>


        @endif

    </nav>



    {{-- ====================================================== --}}
    {{-- PERFORMANCE TAB                                        --}}
    {{-- ====================================================== --}}

    <div
        x-show="section === 'performance'"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="space-y-10">


        {{-- ================================================== --}}
        {{-- ACADEMIC PULSE                                     --}}
        {{-- ================================================== --}}

        <section>

            <div class="mb-6">

                <p
                    class="text-[10px] font-extrabold uppercase
                           tracking-[0.2em] text-slate-400">
                    Academic Pulse
                </p>

                <h2
                    class="mt-1 text-lg font-extrabold
                           tracking-tight text-slate-950">
                    Overall examination performance
                </h2>

            </div>


            <div
                class="analytics-metric-grid grid grid-cols-2 gap-x-6 gap-y-7
                       border-y border-slate-200 py-7
                       lg:grid-cols-4">


                {{-- Average --}}
                <div class="border-r border-slate-200 pr-5">

                    <p
                        class="text-[9px] font-extrabold uppercase
                               tracking-[0.18em] text-slate-400">
                        Average
                    </p>

                    <p
                        class="mt-2 text-4xl font-black
                               tracking-[-0.06em] text-slate-950">
                        {{ number_format($averageScore, 1) }}
                        <span class="text-lg text-slate-300">%</span>
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Overall score
                    </p>

                </div>



                {{-- Highest --}}
                <div class="border-r border-slate-200 pr-5">

                    <p
                        class="text-[9px] font-extrabold uppercase
                               tracking-[0.18em] text-slate-400">
                        Highest
                    </p>

                    <p
                        class="mt-2 text-4xl font-black
                               tracking-[-0.06em] text-slate-950">
                        {{ number_format($highestScore, 1) }}
                        <span class="text-lg text-slate-300">%</span>
                    </p>

                    <p class="mt-1 text-xs text-emerald-600">
                        Best recorded performance
                    </p>

                </div>



                {{-- Lowest --}}
                <div class="border-r border-slate-200 pr-5">

                    <p
                        class="text-[9px] font-extrabold uppercase
                               tracking-[0.18em] text-slate-400">
                        Lowest
                    </p>

                    <p
                        class="mt-2 text-4xl font-black
                               tracking-[-0.06em] text-slate-950">
                        {{ number_format($lowestScore, 1) }}
                        <span class="text-lg text-slate-300">%</span>
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Lowest recorded performance
                    </p>

                </div>



                {{-- Pass Rate --}}
                <div>

                    <p
                        class="text-[9px] font-extrabold uppercase
                               tracking-[0.18em] text-slate-400">
                        Pass Rate
                    </p>

                    <p
                        class="mt-2 text-4xl font-black
                               tracking-[-0.06em] text-indigo-600">
                        {{ number_format($passRate, 1) }}
                        <span class="text-lg text-indigo-300">%</span>
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Completed attempts
                    </p>

                </div>

            </div>

        </section>



        {{-- ================================================== --}}
        {{-- PERFORMANCE VISUALIZATION                          --}}
        {{-- ================================================== --}}

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-12">


            {{-- Average performance --}}
            <div
                class="border border-slate-200 bg-white
                       p-6 lg:col-span-7 aiq-panel aiq-panel-interactive">

                <div class="flex items-start justify-between">

                    <div>

                        <p
                            class="text-[10px] font-extrabold uppercase
                                   tracking-[0.2em] text-indigo-600">
                            Performance Level
                        </p>

                        <h3
                            class="mt-2 text-lg font-extrabold
                                   text-slate-950">
                            Overall score position
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Average performance across completed examination attempts.
                        </p>

                    </div>

                </div>


                <div class="mt-10">

                    <div class="flex items-end justify-between">

                        <div>

                            <p
                                class="text-5xl font-black
                                       tracking-[-0.07em]
                                       text-slate-950">
                                {{ number_format($averageScore, 1) }}
                                <span class="text-xl text-slate-300">%</span>
                            </p>

                            <p
                                class="mt-2 text-xs font-semibold
                                       text-slate-400">
                                Overall average
                            </p>

                        </div>


                        <div class="text-right">

                            <p
                                class="text-[9px] font-extrabold uppercase
                                       tracking-[0.16em] text-slate-400">
                                Pass Rate
                            </p>

                            <p
                                class="mt-1 text-xl font-black
                                       text-indigo-600">
                                {{ number_format($passRate, 1) }}%
                            </p>

                        </div>

                    </div>



                    {{-- Average line --}}
                    <div class="mt-8">

                        <div class="mb-2 flex items-center justify-between">

                            <span
                                class="text-[9px] font-extrabold uppercase
                                       tracking-wider text-slate-400">
                                Average
                            </span>

                            <span class="text-xs font-black text-slate-700">
                                {{ number_format($averageScore, 1) }}%
                            </span>

                        </div>

                        <div class="h-2 overflow-hidden bg-slate-100">

                            <div
                                class="h-full bg-indigo-600
                                       transition-all duration-1000"
                                style="width: {{ $safeAverage }}%">
                            </div>

                        </div>

                    </div>



                    {{-- Pass rate line --}}
                    <div class="mt-6">

                        <div class="mb-2 flex items-center justify-between">

                            <span
                                class="text-[9px] font-extrabold uppercase
                                       tracking-wider text-slate-400">
                                Pass Rate
                            </span>

                            <span class="text-xs font-black text-slate-700">
                                {{ number_format($passRate, 1) }}%
                            </span>

                        </div>

                        <div class="h-2 overflow-hidden bg-slate-100">

                            <div
                                class="h-full bg-emerald-500
                                       transition-all duration-1000"
                                style="width: {{ $safePassRate }}%">
                            </div>

                        </div>

                    </div>



                    <div
                        class="mt-3 flex justify-between
                               text-[9px] font-bold text-slate-300">
                        <span>0</span>
                        <span>25</span>
                        <span>50</span>
                        <span>75</span>
                        <span>100</span>
                    </div>

                </div>

            </div>



            {{-- Outcome breakdown --}}
            <div
                class="bg-slate-950 p-6 text-white
                       lg:col-span-5">

                <p
                    class="text-[10px] font-extrabold uppercase
                           tracking-[0.2em] text-indigo-400">
                    Outcome Breakdown
                </p>

                <h3 class="mt-2 text-lg font-extrabold">
                    Completed attempts
                </h3>

                <p class="mt-1 text-sm text-slate-400">
                    Pass and fail distribution across examinations.
                </p>



                <div class="mt-8 grid grid-cols-2 gap-4">

                    <div class="border-r border-slate-800">

                        <p
                            class="text-[9px] font-extrabold uppercase
                                   tracking-[0.16em] text-slate-500">
                            Passed
                        </p>

                        <p
                            class="mt-2 text-4xl font-black
                                   tracking-[-0.05em]
                                   text-emerald-400">
                            {{ $passed }}
                        </p>

                    </div>


                    <div class="pl-2">

                        <p
                            class="text-[9px] font-extrabold uppercase
                                   tracking-[0.16em] text-slate-500">
                            Failed
                        </p>

                        <p
                            class="mt-2 text-4xl font-black
                                   tracking-[-0.05em]
                                   text-rose-400">
                            {{ $failed }}
                        </p>

                    </div>

                </div>



                <div class="mt-8 border-t border-slate-800 pt-5">

                    <div class="flex items-center justify-between">

                        <span class="text-xs font-semibold text-slate-400">
                            Total completed attempts
                        </span>

                        <span class="text-sm font-black text-white">
                            {{ $totalAttempts }}
                        </span>

                    </div>


                    <div class="mt-4 flex items-center justify-between">

                        <span class="text-xs font-semibold text-slate-400">
                            Students represented
                        </span>

                        <span class="text-sm font-black text-white">
                            {{ $totalStudents }}
                        </span>

                    </div>

                </div>

            </div>

        </section>



        {{-- ================================================== --}}
        {{-- PERFORMANCE MOVEMENT                               --}}
        {{-- ================================================== --}}

        <section>

            <div class="mb-5 flex items-end justify-between">

                <div>

                    <p
                        class="text-[10px] font-extrabold uppercase
                               tracking-[0.2em] text-slate-400">
                        Performance Movement
                    </p>

                    <h3
                        class="mt-1 text-lg font-extrabold
                               text-slate-950">
                        Student changes
                    </h3>

                </div>


                <button
                    type="button"
                    @click="section = 'students'"
                    class="group text-xs font-bold
                           text-indigo-600 transition-all
                           hover:text-indigo-700 active:scale-95 aiq-button aiq-button-secondary">

                    View students

                    <span
                        class="inline-block transition-transform
                               group-hover:translate-x-1">
                        →
                    </span>

                </button>

            </div>


            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">


                {{-- Improving --}}
                <div class="border-t-2 border-emerald-500 bg-white aiq-panel aiq-panel-interactive">

                    <div
                        class="flex items-center justify-between
                               border-b border-slate-200 px-5 py-4">

                        <div>

                            <p
                                class="text-[9px] font-extrabold uppercase
                                       tracking-[0.16em] text-emerald-600">
                                Improving
                            </p>

                            <p class="mt-1 text-sm font-bold text-slate-800">
                                Positive score movement
                            </p>

                        </div>

                        <span
                            class="text-2xl font-black
                                   text-emerald-600">
                            {{ $improvingCount }}
                        </span>

                    </div>


                    <div class="divide-y divide-slate-100">

                        @forelse($improvingStudents as $analytics)

                        <div
                            class="group flex items-center
                                       justify-between px-5 py-4
                                       transition-colors hover:bg-emerald-50/40">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-8 w-8 items-center
                                               justify-center rounded-full
                                               bg-slate-100 text-[9px]
                                               font-black text-slate-500
                                               transition-colors
                                               group-hover:bg-emerald-100
                                               group-hover:text-emerald-700">

                                    {{ strtoupper(substr($analytics->student->fname, 0, 1)) }}
                                    {{ strtoupper(substr($analytics->student->lname, 0, 1)) }}

                                </div>


                                <span
                                    class="text-xs font-bold
                                               text-slate-700">
                                    {{ $analytics->student->fname }}
                                    {{ $analytics->student->lname }}
                                </span>

                            </div>


                            <span
                                class="text-xs font-black
                                           text-emerald-600">
                                ↑ {{ number_format(abs($analytics->improvement_rate), 1) }}%
                            </span>

                        </div>

                        @empty

                        <div class="px-5 py-10 text-center">

                            <p class="text-xs text-slate-400">
                                No improving students yet.
                            </p>

                        </div>

                        @endforelse

                    </div>

                </div>



                {{-- Declining --}}
                <div class="border-t-2 border-rose-500 bg-white aiq-panel aiq-panel-interactive">

                    <div
                        class="flex items-center justify-between
                               border-b border-slate-200 px-5 py-4">

                        <div>

                            <p
                                class="text-[9px] font-extrabold uppercase
                                       tracking-[0.16em] text-rose-600">
                                Declining
                            </p>

                            <p class="mt-1 text-sm font-bold text-slate-800">
                                Negative score movement
                            </p>

                        </div>

                        <span
                            class="text-2xl font-black
                                   text-rose-600">
                            {{ $decliningCount }}
                        </span>

                    </div>


                    <div class="divide-y divide-slate-100">

                        @forelse($decliningStudents as $analytics)

                        <div
                            class="group flex items-center
                                       justify-between px-5 py-4
                                       transition-colors hover:bg-rose-50/40">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-8 w-8 items-center
                                               justify-center rounded-full
                                               bg-slate-100 text-[9px]
                                               font-black text-slate-500
                                               transition-colors
                                               group-hover:bg-rose-100
                                               group-hover:text-rose-700">

                                    {{ strtoupper(substr($analytics->student->fname, 0, 1)) }}
                                    {{ strtoupper(substr($analytics->student->lname, 0, 1)) }}

                                </div>


                                <span
                                    class="text-xs font-bold
                                               text-slate-700">
                                    {{ $analytics->student->fname }}
                                    {{ $analytics->student->lname }}
                                </span>

                            </div>


                            <span
                                class="text-xs font-black
                                           text-rose-600">
                                ↓ {{ number_format(abs($analytics->improvement_rate), 1) }}%
                            </span>

                        </div>

                        @empty

                        <div class="px-5 py-10 text-center">

                            <p class="text-xs text-slate-400">
                                No declining students yet.
                            </p>

                        </div>

                        @endforelse

                    </div>

                </div>

            </div>

        </section>

    </div>



    {{-- ====================================================== --}}
    {{-- STUDENTS TAB                                           --}}
    {{-- ====================================================== --}}

    <div
        x-cloak
        x-show="section === 'students'"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0">


        <section>

            <div
                class="mb-6 flex flex-col gap-4
                       lg:flex-row lg:items-end
                       lg:justify-between">

                <div>

                    <p
                        class="text-[10px] font-extrabold uppercase
                               tracking-[0.2em] text-indigo-600">
                        Student Performance
                    </p>

                    <h2
                        class="mt-1 text-xl font-extrabold
                               tracking-tight text-slate-950">
                        Performance ranking
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Students ordered by their average examination score.
                    </p>

                </div>



                {{-- Search --}}
                <div class="relative w-full lg:w-80">

                    <svg
                        class="absolute left-3.5 top-1/2
                               h-4 w-4 -translate-y-1/2
                               text-slate-400"
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
                        x-model="studentSearch"
                        placeholder="Search student..."
                        class="w-full rounded-xl border
                               border-slate-200 bg-white
                               py-2.5 pl-10 pr-4
                               text-sm font-medium text-slate-700
                               outline-none transition-all
                               placeholder:text-slate-400
                               focus:border-indigo-400
                               focus:ring-4
                               focus:ring-indigo-100/60 aiq-field">

                </div>

            </div>



            {{-- Rankings --}}
            <div class="analytics-table overflow-hidden border border-slate-200 bg-white aiq-panel">

                <div class="overflow-x-auto">

                    <table class="w-full border-collapse text-left">

                        <thead>

                            <tr
                                class="border-b border-slate-200
                                       bg-slate-50 text-[9px]
                                       font-extrabold uppercase
                                       tracking-[0.16em]
                                       text-slate-400">

                                <th class="px-5 py-3.5">
                                    Rank
                                </th>

                                <th class="px-5 py-3.5">
                                    Student
                                </th>

                                <th class="px-5 py-3.5">
                                    Exams
                                </th>

                                <th class="px-5 py-3.5">
                                    Average
                                </th>

                                <th class="px-5 py-3.5">
                                    Performance
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($studentRankings as $student)

                            @php
                            $studentName =
                            $student['student']->fname . ' ' .
                            $student['student']->lname;

                            $studentAverage =
                            (float) $student['average'];

                            $safeStudentAverage =
                            max(0, min(100, $studentAverage));
                            @endphp


                            <tr
                                x-show="
                                        studentSearch === '' ||
                                        @js(strtolower($studentName))
                                            .includes(studentSearch.toLowerCase())
                                    "
                                x-transition.opacity
                                class="group border-b
                                           border-slate-100
                                           transition-colors
                                           last:border-0
                                           hover:bg-indigo-50/30">


                                {{-- Rank --}}
                                <td class="px-5 py-4">

                                    <span
                                        class="text-sm font-black
                                                   {{ $loop->iteration <= 3
                                                        ? 'text-indigo-600'
                                                        : 'text-slate-400' }}">
                                        #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                    </span>

                                </td>



                                {{-- Student --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex h-9 w-9
                                                       shrink-0 items-center
                                                       justify-center rounded-full
                                                       bg-slate-100 text-[9px]
                                                       font-black text-slate-600
                                                       transition-colors
                                                       group-hover:bg-indigo-100
                                                       group-hover:text-indigo-700">

                                            {{ strtoupper(substr($student['student']->fname, 0, 1)) }}
                                            {{ strtoupper(substr($student['student']->lname, 0, 1)) }}

                                        </div>


                                        <div>

                                            <p
                                                class="text-xs font-bold
                                                           text-slate-800">
                                                {{ $studentName }}
                                            </p>

                                            <p
                                                class="mt-0.5 text-[9px]
                                                           font-semibold uppercase
                                                           tracking-wider
                                                           text-slate-400">
                                                Student
                                            </p>

                                        </div>

                                    </div>

                                </td>



                                {{-- Exams --}}
                                <td
                                    class="px-5 py-4
                                               text-xs font-semibold
                                               text-slate-500">
                                    {{ $student['attempts'] }}
                                </td>



                                {{-- Average --}}
                                <td class="px-5 py-4">

                                    <span
                                        class="text-sm font-black
                                                   text-slate-950">
                                        {{ number_format($studentAverage, 1) }}%
                                    </span>

                                </td>



                                {{-- Performance line --}}
                                <td class="min-w-[180px] px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="h-1.5 flex-1
                                                       overflow-hidden
                                                       bg-slate-100">

                                            <div
                                                class="h-full bg-indigo-600
                                                           transition-all
                                                           duration-700"
                                                style="width: {{ $safeStudentAverage }}%">
                                            </div>

                                        </div>


                                        @if($studentAverage >= 75)

                                        <span
                                            class="text-[9px]
                                                           font-extrabold uppercase
                                                           tracking-wider
                                                           text-emerald-600">
                                            Passing
                                        </span>

                                        @else

                                        <span
                                            class="text-[9px]
                                                           font-extrabold uppercase
                                                           tracking-wider
                                                           text-rose-500">
                                            Below 75%
                                        </span>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="px-6 py-16 text-center">

                                    <p
                                        class="text-sm font-bold
                                                   text-slate-600">
                                        No completed examinations yet.
                                    </p>

                                    <p
                                        class="mt-1 text-xs
                                                   text-slate-400">
                                        Student analytics will appear
                                        after examination attempts are completed.
                                    </p>

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </div>

</div>


@push('scripts')
<script>
(() => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    document.querySelectorAll('.analytics-metric-grid > div').forEach(card => {
        card.addEventListener('pointermove', event => {
            if (event.pointerType === 'touch') return;
            const r = card.getBoundingClientRect();
            card.style.setProperty('--tilt-x', `${(0.5-(event.clientY-r.top)/r.height)*4}deg`);
            card.style.setProperty('--tilt-y', `${((event.clientX-r.left)/r.width-0.5)*6}deg`);
        });
        card.addEventListener('pointerleave', () => { card.style.setProperty('--tilt-x','0deg'); card.style.setProperty('--tilt-y','0deg'); });
    });
})();
</script>
@endpush

@endsection